<?php

namespace App\EventSubscriber;

use App\AppEvents;
use App\Entity\Log;
use App\Entity\User;
use App\Event\LogEvent;
use App\Services\RouterosService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppSubscriber implements EventSubscriberInterface
{
    private $em ;
    private $api;

    public function __construct(EntityManagerInterface $em, RouterosService $api)
    {
        $this->em = $em;
        $this->api = $api;
    }

    public static function getSubscribedEvents()
    {
        return [
            AppEvents::APP_UPDATE_LOGS => 'onUpdateLogs',
        ];
    }

    public function onUpdateLogs()
    {
        $getlog = $this->api->comm("/log/print", array("?topics" => "hotspot,info,debug", ));
        error_reporting(0);
        $log = array_reverse($getlog);
        $row = [];
        for ($i = 0; $i < 10; $i++) {
            $mess = explode(":", $log[$i]['message']);
            if (substr($log[$i]['message'], 0, 2) == "->") {
                if($ex = explode(" ", $log[$i]['time']))
                    $time = $ex[1];
                else
                    $time =  $log[$i]['time'];
            
                if (count($mess) > 6) {
                    $addres = $mess[1] . ":" . $mess[2] . ":" . $mess[3] . ":" . $mess[4] . ":" . $mess[5] . ":" . $mess[6];
                } else {
                    $addres = $mess[1];
                }

                if (count($mess) > 6) {
                    $message = str_replace("trying to", "", $mess[7] . " " . $mess[8] . " " . $mess[9] . " " . $mess[10]);
                } else {
                    $message = str_replace("trying to", "", $mess[2] . " " . $mess[3] . " " . $mess[4] . " " . $mess[5]);
                }
                if($addres[0] === " "){
                    $addres = substr($addres, 1);
                }
                $array =  explode(" ", $addres);
            

                if( $time && isset($array[0]) && isset($array[1]) && null === $this->em->getRepository(Log::class)->findOneBy([
                    'time' => new DateTime($time),
                    'ip' => $array[1],
                    'user' => $this->em->getRepository(User::class)->findOneBy(['username' => $array[0]]),
                    'message' => $message   
                ])){
                    $logObj = new Log();
                    $logObj
                        ->setIp( $array[1])
                        ->setTime(new DateTime($time))
                        ->setUser($this->em->getRepository(User::class)->findOneBy(['username' => $array[0]]))
                        ->setMessage($message);
                    
                        $this->em->persist($logObj);
                        $this->em->flush();
                }
            }
        }
    }
}
