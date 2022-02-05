<?php

namespace App\Controller;

use App\Entity\Package;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'username' => $request->request->get('user')
        ]);
        if (!$user) return new JsonResponse([-1]);
        $debits = $em->getRepository(Package::class)->findByUsername($user);

        return new JsonResponse([
            $debits[0]['deuda']
        ]);
    }
}
