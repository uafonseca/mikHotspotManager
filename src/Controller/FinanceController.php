<?php

namespace App\Controller;

use App\Entity\Package;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Undocumented class
 */
class FinanceController extends AbstractController
{
    /**
     * @Route("/finance", name="finance")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $today = $em->getRepository(Package::class)->allToday($this->getUser());

        return $this->render('finance/index.html.twig', [
            'alltoday' => $today,
        ]);
    }
}
