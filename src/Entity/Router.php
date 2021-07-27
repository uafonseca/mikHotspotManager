<?php

namespace App\Entity;

use App\Repository\RouterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RouterRepository::class)
 */
class Router
{
    const LOGIN_TYPE_USER_AND_PASS = 'Usuario y contraseña';
    const LOGIN_TYPE_MAC_AS_USER_AND_PASS = 'MAC como Usuario y contraseña';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $routerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $Interface;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hotspotloginType = self::LOGIN_TYPE_USER_AND_PASS;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRouterName(): ?string
    {
        return $this->routerName;
    }

    public function setRouterName(string $routerName): self
    {
        $this->routerName = $routerName;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getInterface(): ?string
    {
        return $this->Interface;
    }

    public function setInterface(string $Interface): self
    {
        $this->Interface = $Interface;

        return $this;
    }

    public function getHotspotloginType(): ?string
    {
        return $this->hotspotloginType;
    }

    public function setHotspotloginType(string $hotspotloginType): self
    {
        $this->hotspotloginType = $hotspotloginType;

        return $this;
    }
}
