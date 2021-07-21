<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use App\Services\RouterosService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/userprofile")
 */
class UserProfileController extends AbstractController
{
    private RouterosService $api;


    /**
     * Undocumented function
     *
     * @param RouterosService $api
     */
    public function __construct(RouterosService $api)
    {
        $this->api = $api;
    }

    /**
     * @Route("/", name="user_profile_index", methods={"GET"})
     */
    public function index(UserProfileRepository $userProfileRepository): Response
    {
        $id = $this->api->comm("/ip/hotspot/user/profile/print");
        // dd($id);
        return $this->render('user_profile/index.html.twig', [
            'user_profiles' => $userProfileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_profile_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $userProfile = new UserProfile();
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $id = $this->api->comm("/ip/hotspot/user/profile/add", [
                    "name" => $userProfile->getName(),
                    "address-pool" => $userProfile->getAddresPool(),
                    "rate-limit" => $userProfile->getRateLimit(),
                ]);

                $userProfile->setMikId($id);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($userProfile);
                $entityManager->flush();

                return $this->redirectToRoute('user_profile_index', [], Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return $this->renderForm('user_profile/new.html.twig', [
            'user_profile' => $userProfile,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="user_profile_show", methods={"GET"})
     */
    public function show(UserProfile $userProfile): Response
    {
        return $this->render('user_profile/show.html.twig', [
            'user_profile' => $userProfile,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserProfile $userProfile): Response
    {
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                try {
                $this->api->comm("/ip/hotspot/user/profile/set", [
                    "name" => $userProfile->getName(),
                    "address-pool" => $userProfile->getAddresPool(),
                    "rate-limit" => $userProfile->getRateLimit(),
                ]);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_profile_index', [], Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return $this->renderForm('user_profile/edit.html.twig', [
            'user_profile' => $userProfile,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="user_profile_delete", methods={"POST"})
     */
    public function delete(Request $request, UserProfile $userProfile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userProfile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userProfile);
            try {
                 $this->api->comm("/ip/hotspot/user/profile/remove",[
                     '.id'=> $userProfile->getMikId()
                 ]);
            } catch (Exception $e) {
                die($e->getMessage());
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_profile_index', [], Response::HTTP_SEE_OTHER);
    }
}
