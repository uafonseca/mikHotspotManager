<?php

namespace App\Controller;

use App\Entity\Package;
use App\Repository\RouterRepository;
use App\Services\RouterosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    private $api;

    public function __construct(RouterosService $api)
    {
        $this->api = $api;
    }

    /**
     * @Route("/", name="home")
     *
     * @IsGranted("ROLE_USER")
     */
    public function index(RouterRepository $routerRepository): Response
    {
        // dd($count = $this->api->comm("/ip/hotspot/cookie/print", [
            
        // ]));

        if (count($routerRepository->findAll()) === 0) {
            $this->addFlash('error', 'Es necesario configurar su router');
            return $this->redirectToRoute('router_new');
        }
        $count = $this->api->comm("/ip/hotspot/user/print", [
            'count-only'=>""
        ]);

        $interface = $this->api->getRouter()->getInterface();
        
        return $this->render('main/index.html.twig', [
            'count' => $count,
            'interface' => $interface
        ]);
    }

    public function controlSidebarSettings(Request $originalRequest)
    {
        return $this->render('sidebar/settings.html.twig', []);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return void
     * 
     * @Route("/ping", name="ping", methods={"POST","GET"}, options={"expose" = true})
     */
    public function ping($url='https://1.1.1.1')
    {
        $result = false;
        if ($url == null) {
            return new JsonResponse($result);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode>=200 && $httpcode<300) {
            $result =  true;
        } else {
            $result =  false;
        }
        return new JsonResponse($result);
    }


    /**
     * Undocumented function
     *
     * @return void
     * 
     * @Route("/debts", name="debts")
     */
    public function myDebts(Request $request){
        
        $em = $this->getDoctrine()->getManager();

        if(null != $idPay = $request->query->get('pay') ){
            $pack = $em->getRepository(Package::class)->find($idPay);
            $pack->setDebt(false);
            $em->flush();

            return $this->redirectToRoute('debts');
        }
        

        $debts = $em->getRepository(Package::class)->myDebts($this->getUser());

        return $this->render('main/debts.html.twig', [
            'debts' => $debts
        ]);
    }
}
