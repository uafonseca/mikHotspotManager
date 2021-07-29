<?php

namespace App\Controller;

use App\Entity\Package;
use App\Entity\Router;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\RouterosService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private  $api;

    private $passwordEncoder;

    /**
     * Undocumented function
     *
     * @param RouterosService $api
     */
    public function __construct(RouterosService $api, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->api = $api;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $users = $this->api->comm("/ip/hotspot/user/print");
        for($i = 0; $i < count($users); $i ++){
            /** @var User $local */
            $local = $em->getRepository(User::class)->findOneBy(['mikId'=> $users[$i]['.id']]);
            if($local != null){
                $users[$i]['localId'] = $local->getId();
                $users[$i]['mac'] = $local->getMacAddress();
                $users[$i]['nombre'] = $local->getUsername();
            }
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            if($user->getIsLocal()){
                $password = $this->passwordEncoder->encodePassword ($user, $user->getPlainPassword());
				$user->setPassword ($password);
            }else{
                $user->setPassword($user->getPlainPassword());
            }
            if(intval($form->get("time")->getData()) === 0 && !$this->isGranted('ROLE_SUPER_ADMIN'))
                $time = '0';
            else
                $time = $this->api->calculateTimeFronMoney($form->get("time")->getData(), $user->getProfile()->getPrice());
            try{
                $username = $user->getUsername();
                $password = $user->getPlainPassword();
                if($this->api->getRouter()->getHotspotloginType() === Router::LOGIN_TYPE_MAC_AS_USER_AND_PASS){
                    $username = $user->getMacAddress();
                    $password = $user->getMacAddress();
                }
                $id = $this->api->comm("/ip/hotspot/user/add", [
                    "name" => $username,
                    "password" => $password,
                    "profile" => $user->getProfile()->getName(),
                    "limit-uptime" => $time
                ]);
                $user->setMikId($id);

                if($time != ""){
                    $pack =  new Package();
                    $pack
                        ->setUser($user)
                        ->setDebt(false)
                        ->setPrice($form->get("time")->getData());
                    $entityManager->persist($pack);
                    $user->addPack($pack);
                }
                
                $entityManager->flush();

                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }catch (Exception $e) {
                die($e->getMessage());
            }
           
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'mac_as_user_and_pass' => $this->api->getRouter()->getHotspotloginType() === Router::LOGIN_TYPE_MAC_AS_USER_AND_PASS
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                if($user->getIsLocal()){
                    $password = $this->passwordEncoder->encodePassword ($user, $user->getPlainPassword());
                    $user->setPassword ($password);
                }else{
                    $user->setPassword($user->getPlainPassword());
                }
                $username = $user->getUsername();
                $password = $user->getPlainPassword();
                if($this->api->getRouter()->getHotspotloginType() === Router::LOGIN_TYPE_MAC_AS_USER_AND_PASS){
                    $username = $user->getMacAddress();
                    $password = $user->getMacAddress();
                }
                $this->api->comm("/ip/hotspot/user/set", [
                    ".id" => $user->getMikId(),
                    "name" => $username,
                    "password" => $password,
                    "profile" => $user->getProfile()->getName(),
                    "limit-uptime" => $form->get("time")->getData() == "" ? 0 : $form->get("time")->getData()
                ]);
                $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }
            catch(Exception $e){
                die($e->getMessage());
            }
            
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'mac_as_user_and_pass' => $this->api->getRouter()->getHotspotloginType() === Router::LOGIN_TYPE_MAC_AS_USER_AND_PASS
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            try {
                $this->api->comm("/ip/hotspot/user/remove",[
                    '.id'=> $user->getMikId()
                ]);
           } catch (Exception $e) {
               die($e->getMessage());
           }
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

}
