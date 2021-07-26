<?php

namespace App\Services;

use App\Entity\Package;
use App\Entity\Router;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class RouterosService
{
    private RouterosAPI $api;

    private EntityManagerInterface $em;

    private Security $securiry;

  /**
   * Undocumented function
   *
   * @param RouterosAPI $api
   * @param EntityManagerInterface $em
   * @param Security $securiry
   */
    public function __construct(RouterosAPI $api, EntityManagerInterface $em, Security $securiry)
    {
        $this->em = $em;
        $this->api = $api;
        $this->securiry = $securiry;
    }


    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function connect():bool
    {
        /** @var Router $mikrotick */
        if (null != $mikrotick = $this->getRouter()) {
            return $this->api->connect($mikrotick->getIp(), $mikrotick->getUsername(), $mikrotick->getPassword());
        }
        return false;
    }

    /**
     * Undocumented function
     *
     * @return Router
     */
    public function getRouter()
    {
        $mikrotick = $this->em->getRepository(Router::class)->findAll();

        return count($mikrotick) > 0 ? $mikrotick[0] : null;
    }


    /**
     * Undocumented function
     *
     * @param string $query
     * @return array
     */
    public function comm(string $query, array $params = [])
    {
        if ($this->connect()) {
            $result = $this->api->comm($query, $params);

            $this->api->disconnect();

            return $result;
        }
        return [];
    }

    /**
     * Undocumented function
     *
     * @param [type] $dtm
     * @return string
     */
    public function formatDTM($dtm):string
    {
        return $this->api->formatDTM($dtm);
    }

    /**
     * Undocumented function
     *
     * @param [type] $size
     * @param integer $decimals
     * @return string
     */
    public function formatBytes($size, $decimals = 0):string
    {
        return $this->api->formatBytes($size, $decimals);
    }

    /**
     * Undocumented function
     *
     * @return RouterosAPI
     */
    public function baseApi():RouterosAPI
    {
        return $this->api;
    }

    public function calculateTimeFronMoney($money, $price)
    {  
        if(intval($money) === 0) return "0";
        $d = date_create('00:00:00');
        return $d->add(new DateInterval('PT' . intval((($money / ($price / 60)) / 60) * 60) . 'M'))->format('h:i');
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    public function countDebs():int{
        $user = $this->securiry->getUser();
        $debts = $this->em->getRepository(Package::class)->myDebts($user);
        return count($debts);
    }
}
