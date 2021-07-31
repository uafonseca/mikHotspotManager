<?php

namespace App\Controller;

use App\Entity\Package;
use App\Entity\User;
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

        $users = $em->getRepository(User::class)->findAll();

        $filters = $em->getRepository(Package::class)->findByFilters($request->query->all());
        return $this->render('finance/index.html.twig', [
            'alltoday' => $today,
            'users' => $users,
            'filters' => $filters
        ]);
    }
}
