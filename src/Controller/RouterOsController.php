<?php

namespace App\Controller;

use App\Entity\Investmenst;
use App\Entity\Package;
use App\Entity\Router;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Services\RouterosAPI;
use App\Services\RouterosService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/routerOsAPI")
 */
class RouterOsController extends AbstractController
{
    private $api;

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
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/connect", name="routerOs-connect")
     */
    public function connect(Request $request):Response
    {
        if ($this->api->connect()) {
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

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/trafic", name="routerOs-trafic", options={"expose" = true})
     */
    public function trafic(Request $request):Response
    {
        if ($this->api->connect()) {
            $interface = $this->api->getRouter()->getInterface();
            $getinterfacetraffic = $this->api->comm("/interface/monitor-traffic", array(
                "interface" => $interface ,
                "once" => "",
                ));
            $rows = array();
            $rows2 = array();

            $ftx = $getinterfacetraffic[0]['tx-bits-per-second'];
            $frx = $getinterfacetraffic[0]['rx-bits-per-second'];
            
            $rows['name'] = 'Tx';
            $rows['data'][] = $ftx;
            $rows2['name'] = 'Rx';
            $rows2['data'][] = $frx;

            $result = array();

            array_push($result, $rows);
            array_push($result, $rows2);
            $ctive = $this->api->comm("/ip/hotspot/active/print",[
                'count-only'=>""
            ]);

            return new JsonResponse([
                'result' => $result,
                'actives' => $ctive
            ]);
        }
        return new Response(500);
    }

  

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/active", name="routerOs-active", options={"expose" = true})
     */
    public function activeUsers(Request $request):Response
    {
        if ($this->api->connect()) {
            $gethotspotactive = $this->api->comm("/ip/hotspot/active/print");
            $em = $this->getDoctrine()->getManager();
            $output = [];

            foreach ($gethotspotactive as $item) {
                $user = $em->getRepository(User::class)->findOneBy(['username'=>$item['user']]);
                $id = '';
                if($user)
                    $id= $user->getId();
                $action1 = '<a href="#" class="add-time" data-user-id="'.$id.'" data-tippy-content="Acreditar"><i class="fa fa-clock"></a>';
                $output[] = [
                    $item['user'],
                    $item['address'],
                    $item['mac-address'],
                    $this->api->formatDTM($item['uptime']),
                    // $this->api->formatBytes($item['bytes-in'], 2),
                    $this->api->formatBytes($item['bytes-out'], 2),
                    isset($item['session-time-left']) ? $this->api->formatDTM($item['session-time-left']) : '',
                    $item['login-by'],
                    $action1
                ];
            }
            
            return new JsonResponse([
            'data' => $output,
        ]);
        }
        return new Response(500);
    }


    public function syncProfiles()
    {
        $em = $this->getDoctrine()->getManager();
        $hotspotProfiles = $this->api->comm("/ip/hotspot/user/profile/print");
        $counter = 0;
        foreach ($hotspotProfiles as $row) {
            $localProfile = $em->getRepository(UserProfile::class)->findOneBy(['name'=>$row['name']]);
            if ($localProfile === null) {
                $localProfile = new UserProfile();
            }
            $localProfile
                ->setMikId($row['.id'])
                ->setName($row['name'])
                ->setAddresPool(isset($row['address-pool']) ? $row['address-pool'] : '')
                ->setRateLimit(isset($row['rate-limit']) ? $row['rate-limit'] : '')
                ;
            $em->persist($localProfile);
            $counter ++;
        }
        $em->flush();

        return $counter;
    }

    /**
     * Undocumented function
     *
     * @return void
     *
     * @Route("/sync-users", name="routerOs-sync-users", options={"expose" = true})
     */
    public function synUsers()
    {
        $profiles = $this->syncProfiles();
        $em = $this->getDoctrine()->getManager();
        $hotspotUsers = $this->api->comm("/ip/hotspot/user/print");
        $counter = 0;
        foreach ($hotspotUsers as $row) {
            $localUser = $em->getRepository(User::class)->findOneBy(['username'=>$row['name']]);
            $exist = true;
            if (!$localUser instanceof User) {
                $localUser = new User();
                $exist = false;
            }
            $localUser
                    ->setMikId($row['.id'])
                    ->setUsername($row['name'])
                    ->setPlainPassword(isset($row['password'])? $row['password'] : '')
                    ->setPassword($localUser->getPlainPassword())
                    ->setIsLocal(false)
                    ->setMikId($row['.id']);

            if($exist && $localUser->getIsLocal()){
                $password = $this->passwordEncoder->encodePassword ($localUser, $localUser->getPlainPassword());
				$localUser->setPassword ($password);
            }

                   
            if (isset($row['profile'])) {
                $localUser->setProfile($em->getRepository(UserProfile::class)->findOneBy(['name'=>$row['profile']]))
                    ;
            }
            $em->persist($localUser);
            $counter ++;
        }
        $em->flush();
        return new JsonResponse([
            'users' => $counter,
            'profiles' => $profiles
        ]);
    }

    /**
     * Undocumented function
     *
     * @return Response
     *
     * @Route("/add-time/{id}", name="routerOs-add-time", options={"expose" = true})
     */
    public function addTime(User $user):Response
    {
        return $this->render('main/add-time.html.twig', [
            'user' => $user,
            'minPrice' => $user->getProfile()->getPrice()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     *
     * @Route("/save-time", name="routerOs-save-time", options={"expose" = true})
     */
    public function saveTime(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $deuda = $request->query->get('deuda');

        $deudaPrice = $request->query->get('deudaPrice');
        
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        $time = $this->api->calculateTimeFronMoney($request->query->get('time'),$user->getProfile()->getPrice());
        
        
        if (null != $user ) {
            $mikUser = $this->api->comm('/ip/hotspot/user/print', [
            "?.id" => $user->getMikId()
           ]);
            $newTime = $time;
            $uuptime = $this->api->formatDTM($mikUser[0]['uptime']);
            if (isset($mikUser[0]['limit-uptime'])) {
                $uptimelimit = $this->api->formatDTM($mikUser[0]['limit-uptime']);
            } else {
                $uptimelimit = 0;
            }
            if ($uptimelimit != 0) {
                if (date($uuptime) < date($uptimelimit)) {
                    $d1 = date_create($uuptime);
                    $d2 = date_create($uptimelimit);
                    $dif = date_diff($d1, $d2);
                    $newTime = new DateTime($this->api->formatDTM($time));
                    $newTime->add($dif);
                }
            }
            $this->api->comm("/ip/hotspot/user/set", array(
                ".id" => $user->getMikId(),
                "limit-uptime" => $newTime instanceof DateTime ? $newTime->format('h:i') : $newTime,
              ));
            $this->api->comm("/ip/hotspot/user/reset-counters", array(
                ".id" => $user->getMikId(),
              ));
            $pack =  new Package();
            $pack
                ->setUser($user)
                ->setDebt($deuda === 'true')
                ->setPrice($request->query->get('time'));
            if($deuda === 'true')
                $pack->setPriceDebt($deudaPrice);
            $em->persist($pack);
            $user->addPack($pack);
            $em->flush();
            return new JsonResponse([
                'type' => 'success',
                'message' => 'Monto adicionado!'
            ]);
        }
        die('ERROR');
    }


    /**
     * Undocumented function
     *
     * @return void
     *
     * @Route("/logs", name="routerOs-logs", options={"expose" = true})
     */
    public function hotsPotLog()
    {
        $getlog = $this->api->comm("/log/print", array("?topics" => "hotspot,info,debug", ));

        $log = array_slice(array_reverse($getlog), 0, 10);
        $row = [];
        for ($i = 0; $i < 10; $i++) {
            $mess = explode(":", $log[$i]['message']);
            if (substr($log[$i]['message'], 0, 2) == "->") {
                $time = str_replace(" ", "<br>", $log[$i]['time']);
            }
           
            $message = isset($mess[2]) ? str_replace("trying to ", "", $mess[2]) :'';
            if (isset($mess[3])) {
                $message.":".$mess[3];
            }
            
            
            $ip = substr($mess[1], 1);
            $ip = str_replace(" ", "<br>", $ip);

            $row[] = [
                'time' => $time,
                'ip' => $ip,
                'message' => $message
            ];
        }
        return $this->render('main/logs.html.twig', [
            'logs' => $log
        ]);
    }
    /**
     * Undocumented function
     *
     * @return void
     * @Route("/finance-today", name="user_finance-today", methods={"POST","GET"}, options={"expose" = true})
     */
    public function financeToday(){
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $today = $em->getRepository(Package::class)->createdToday($user);
        $thisMonth = $em->getRepository(Package::class)->createdThisMonth($user);

        $todayAll = $em->getRepository(Package::class)->createdToday($user, false);
        $thisMonthAll = $em->getRepository(Package::class)->createdThisMonth($user, false);

        $investmenst = $em->getRepository(Investmenst::class)->investmentThisMonth($user);

        $totalPay = 0;
        $gananciaNeta = 0;

        $todayAllMinus = 0;

        $total = 0;
        
        if(!$this->isGranted('ROLE_SUPER_ADMIN'))
        {
            if(!isset($thisMonth[0][1]))
                $thisMonth[0][1] = 0;
            $totalPay = $this->getUser()->getComision() * intval($thisMonth[0][1]) / 100;
            $gananciaNeta = intval($thisMonth[0][1]) - $totalPay;
        }else{
            $admins = $em->getRepository(User::class)->findBy(['isLocal' => true]);
            if( isset($thisMonthAll[0]) && $thisMonthAll[0][1]){
                $todayAllMinus = $thisMonthAll[0][1];
               
                /** @var User $admin */
                foreach($admins as $admin){
                    if(!in_array('ROLE_SUPER_ADMIN', $admin->getRoles())){
                        $percent = $admin->getComision() * intval($todayAllMinus) / 100;
                        $total = $total+ $percent;
                    }
                }
            }
        }

        return new JsonResponse([
            'today' => isset($today[0]) && $today[0][1] != null ? $today [0][1] : 0,
            'thisMonth' => isset($thisMonth[0]) && $thisMonth[0][1] != null  ? $thisMonth [0][1] : 0,

            'todayAll' => isset($todayAll[0]) && $todayAll[0][1] != null ? $todayAll [0][1] : 0,
            'thisMonthAll' => isset($thisMonthAll[0]) && $thisMonthAll[0][1] != null  ? $thisMonthAll [0][1] : 0,

            'investmenst'  => isset($investmenst[0]) && $investmenst[0][1] != null  ? $investmenst [0][1] : 0,

            'totalPay' => $totalPay,
            'gananciaNeta' => $gananciaNeta,

            'todayAllMinus' => $total + (isset($thisMonth[0]) && $thisMonth[0][1] != null  ? $thisMonth [0][1] : 0)
        ]);
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return void
     * 
     * @Route("/disconect/{id}", name="routerOs-disconect-user", methods={"POST","GET"}, options={"expose" = true})
     */
    public function disconect(User $user){
        try{
            $this->api->comm('/ip/hotspot/active/remove',[
                '.id'=> $user->getMikId()
            ]);
            return new JsonResponse([
                'message' => 'Usuario desconectado'
            ]);
        }catch(Exception $e){
            return new JsonResponse([
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * Undocumented function
     *
     * @return void
     * @Route("/hosts", name="routerOs-hosts")
     */
    public function host(){
        $hosts = $this->api->comm('/ip/hotspot/host/print',[]);
        return $this->render('pages/hosts.html.twig',[
            'hosts' => $hosts
        ]);
    }

    /**
     * Undocumented function
     *
     * @return void
     * 
     * @Route("/dhcp", name="routerOs-dhcp")
     */
    public function dhcpLeases(){
        $hosts = $this->api->comm('/ip/dhcp-server/lease/print',[]);
        return $this->render('pages/dhcp.html.twig',[
            'hosts' => $hosts
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     * 
     * @Route("/update-comision", name="update-comisions", options={"expose" = true})
     */
    public function updateComision(Request $request){
        $id = $request->query->get('user');
        $val = $request->query->get('val');

        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        $user->setComision(intval($val));

        $em->flush();

        return new JsonResponse([
            'type' => 'success',
            'message' => 'Datos guardados'
        ]);
    }
}
