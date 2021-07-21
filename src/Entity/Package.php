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

    public function __construct()
    {
        $this->user = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

   
}
