<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonimusController extends AbstractController
{
    /**
     * @Route("/annonimus", name="annonimus")
     */
    public function index(): Response
    {
        return new JsonResponse([
            'type' => 'success',
            'online' => $this->getUser() != null
        ]);
    }
}
