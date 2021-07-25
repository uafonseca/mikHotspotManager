<?php

namespace App\Controller;

use App\Entity\Investmenst;
use App\Form\InvestmenstType;
use App\Repository\InvestmenstRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/investmenst")
 */
class InvestmenstController extends AbstractController
{
    /**
     * @Route("/", name="investmenst_index", methods={"GET"})
     */
    public function index(InvestmenstRepository $investmenstRepository): Response
    {
        return $this->render('investmenst/index.html.twig', [
            'investmensts' => $investmenstRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="investmenst_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $investmenst = new Investmenst();
        $form = $this->createForm(InvestmenstType::class, $investmenst,[
            'action' => $this->generateUrl('investmenst_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($investmenst);
            $entityManager->flush();

            return new JsonResponse([
                'type' => 'success',
                'message' => 'Datos guardados'
            ]);
        }

        return $this->renderForm('investmenst/new.html.twig', [
            'investmenst' => $investmenst,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="investmenst_show", methods={"GET"})
     */
    public function show(Investmenst $investmenst): Response
    {
        return $this->render('investmenst/show.html.twig', [
            'investmenst' => $investmenst,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="investmenst_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Investmenst $investmenst): Response
    {
        $form = $this->createForm(InvestmenstType::class, $investmenst);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('investmenst_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('investmenst/edit.html.twig', [
            'investmenst' => $investmenst,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="investmenst_delete", methods={"POST"})
     */
    public function delete(Request $request, Investmenst $investmenst): Response
    {
        if ($this->isCsrfTokenValid('delete'.$investmenst->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($investmenst);
            $entityManager->flush();
        }

        return $this->redirectToRoute('investmenst_index', [], Response::HTTP_SEE_OTHER);
    }
}
