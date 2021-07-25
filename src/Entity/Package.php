<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use App\Traits\BlameableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackageRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Package
{
    use BlameableEntityTrait;
    use TimestampableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="packs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $debt;

        /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceDebt;

    public function __construct()
    {
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDebt(): ?bool
    {
        return $this->debt;
    }

    public function setDebt(bool $debt): self
    {
        $this->debt = $debt;

        return $this;
    }

   

    /**
     * Get the value of priceDebt
     */ 
    public function getPriceDebt()
    {
        return $this->priceDebt;
    }

    /**
     * Set the value of priceDebt
     *
     * @return  self
     */ 
    public function setPriceDebt($priceDebt)
    {
        $this->priceDebt = $priceDebt;

        return $this;
    }
}
