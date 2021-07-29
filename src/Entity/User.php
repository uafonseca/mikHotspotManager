<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\BlameableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="Existe un usuario registrado con este nombre")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Por favor ingrese un usuario")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean", nullable =true)
     */
    private $isLocal = false;

    /**
     * @ORM\ManyToOne(targetEntity=UserProfile::class, inversedBy="users")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $profile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mikId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=Package::class, mappedBy="user")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $packs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comision = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $macAddress;

    /**
     * @ORM\OneToMany(targetEntity=Log::class, mappedBy="user")
     */
    private $logs;

    public function __construct()
    {
        $this->packs = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Undocumented function
     *
     * @param string $role
     * @return void
     */
    public function addRole(string $role){
        if(!in_array($role, $this->roles)){
            $this->roles[] = $role;
        }
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getIsLocal(): ?bool
    {
        return $this->isLocal;
    }

    public function setIsLocal(bool $isLocal): self
    {
        $this->isLocal = $isLocal;

        return $this;
    }

    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    public function setProfile(?UserProfile $profile): self
    {
        $this->profile = $profile;

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection|Package[]
     */
    public function getPacks(): Collection
    {
        return $this->packs;
    }

    public function addPack(Package $pack): self
    {
        if (!$this->packs->contains($pack)) {
            $this->packs[] = $pack;
            $pack->setUser($this);
        }

        return $this;
    }

    public function removePack(Package $pack): self
    {
        if ($this->packs->removeElement($pack)) {
            // set the owning side to null (unless already changed)
            if ($pack->getUser() === $this) {
                $pack->setUser(null);
            }
        }

        return $this;
    }

    public function getComision(): ?int
    {
        return $this->comision;
    }

    public function setComision(int $comision): self
    {
        $this->comision = $comision;

        return $this;
    }

    public function getMacAddress(): ?string
    {
        return $this->macAddress;
    }

    public function setMacAddress(?string $macAddress): self
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

   
}
