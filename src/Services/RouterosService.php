<?php

namespace App\Services;

use App\Entity\Router;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class RouterosService
{
    private RouterosAPI $api;

    private EntityManagerInterface $em;

    /**
     * Undocumented function
     *
     * @param RouterosAPI $api
     * @param EntityManagerInterface $em
     */
    public function __construct(RouterosAPI $api, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->api = $api;
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
     * @return void
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
        /** suponiendo que 15 es el precio x hora */
        $d = date_create('00:00:00');
        return $d->add(new DateInterval('PT' . intval((($money / ($price / 60)) / 60) * 60) . 'M'))->format('h:i');
    }
}
