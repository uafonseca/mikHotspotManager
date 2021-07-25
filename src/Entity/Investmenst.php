<?php

namespace App\Entity;

use App\Repository\InvestmenstRepository;
use App\Traits\BlameableEntityTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvestmenstRepository::class)
 *  @ORM\HasLifecycleCallbacks
 */
class Investmenst
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
    private $mount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMount(): ?int
    {
        return $this->mount;
    }

    public function setMount(int $mount): self
    {
        $this->mount = $mount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
