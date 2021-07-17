<?php

namespace App\Controller;

use App\Services\RouterosAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/routerOsAPI")
 */
class RouterOsController extends AbstractController{


    private $api;

    /**
     * Undocumented function
     *
     * @param RouterosAPI $api
     */
    public function __construct(RouterosAPI $api)
    {
        $this->api = $api;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     * 
     * @Route("/connect", name="routerOs-connect")
     */
    public function connect(Request $request):Response{
        if ($this->api->connect($request->request->get('ip'), $request->request->get('user'), $request->request->get('password'))){
            return new JsonResponse([
                'status' => 200,
                'message' => 'Conectado!'
            ]);
          }
          return new JsonResponse([
              'status' => 500,
              'message' => 'Error!'
          ]);
    }
}