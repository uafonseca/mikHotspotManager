<?php

namespace App\Controller;

use App\Entity\Router;
use App\Entity\User;
use App\Form\RouterType;
use App\Repository\RouterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/router")
 */
class RouterController extends AbstractController
{
    /**
     * @Route("/", name="router_index", methods={"GET"})
     */
    public function index(RouterRepository $routerRepository): Response
    {
        return $this->render('router/index.html.twig', [
            'routers' => $routerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="router_new", methods={"GET","POST"})
     */
    public function new(Request $request, RouterRepository $routerRepository): Response
    {
        $exist = false;
        if($router = $routerRepository->findAll()){
            $router = $router[0];
            $exist = true;
        }else{
            $router = new Router();
        }
        $form = $this->createForm(RouterType::class, $router);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($router);
            $entityManager->flush();

            return $this->redirectToRoute('router_new', [], Response::HTTP_SEE_OTHER);
        }

        $admins = $entityManager->getRepository(User::class)->findBy([
            'isLocal' => true
        ]);

        return $this->renderForm('router/new.html.twig', [
            'router' => $router,
            'form' => $form,
            'exist' => $exist,
            'admins' => $admins
        ]);
    }

    /**
     * @Route("/{id}", name="router_show", methods={"GET"})
     */
    public function show(Router $router): Response
    {
        return $this->render('router/show.html.twig', [
            'router' => $router,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="router_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Router $router): Response
    {
        $form = $this->createForm(RouterType::class, $router);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('router_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('router/edit.html.twig', [
            'router' => $router,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="router_delete", methods={"POST"})
     */
    public function delete(Request $request, Router $router): Response
    {
        if ($this->isCsrfTokenValid('delete'.$router->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($router);
            $entityManager->flush();
        }

        return $this->redirectToRoute('router_index', [], Response::HTTP_SEE_OTHER);
    }
}
