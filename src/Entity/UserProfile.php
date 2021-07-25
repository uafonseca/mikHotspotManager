<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use App\Traits\BlameableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserProfileRepository::class)
 * @UniqueEntity(fields={"name"}, message="Existe un perfil registrado con este nombre")
 * @ORM\HasLifecycleCallbacks
 */
class UserProfile
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
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(message="Por favor ingrese un nombre para el perfil")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $addresPool;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $rateLimit;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profile")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mikId;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddresPool(): ?string
    {
        return $this->addresPool;
    }

    public function setAddresPool(?string $addresPool): self
    {
        $this->addresPool = $addresPool;

        return $this;
    }

    public function getRateLimit(): ?string
    {
        return $this->rateLimit;
    }

    public function setRateLimit(?string $rateLimit): self
    {
        $this->rateLimit = $rateLimit;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfile($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfile() === $this) {
                $user->setProfile(null);
            }
        }

        return $this;
    }

    public function getMikId(): ?string
    {
        return $this->mikId;
    }

    public function setMikId(?string $mikId): self
    {
        $this->mikId = $mikId;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getRoles(): ?array
    {
        return is_array($this->roles) ? $this->roles : [];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
