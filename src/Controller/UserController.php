<?php

namespace App\Controller;

use App\Entity\Package;
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
    private RouterosService $api;

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
            $local = $em->getRepository(User::class)->findOneBy(['mikId'=> $users[$i]['.id']]);
            if($local != null)
                $users[$i]['localId'] = $local->getId();
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
            try{
                $this->api->comm("/ip/hotspot/user/add", [
                    "name" => $user->getUsername(),
                    "password" => $user->getPlainPassword(),
                    "profile" => $user->getProfile()->getName(),
                    "limit-uptime" => $form->get("time")->getData() == "" ? 0 : $form->get("time")->getData()
                ]);

                
                $entityManager->flush();

                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }catch (Exception $e) {
                die($e->getMessage());
            }
           
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
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
                $this->api->comm("/ip/hotspot/user/set", [
                    ".id" => $user->getMikId(),
                    "name" => $user->getUsername(),
                    "password" => $user->getPlainPassword(),
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
