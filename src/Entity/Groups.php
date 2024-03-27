<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users; // Import the Users entity class

/**
 * Groups
 *
 * @ORM\Table(name="groups", indexes={@ORM\Index(name="fk_groups1", columns={"id_user"})})
 * @ORM\Entity
 */
class Groups
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_group", type="text", length=0, nullable=false)
     */
    private $nomGroup;
     /**
     * @var string
     *
     * @ORM\Column(name="image", type="text", length=0, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="description_group", type="text", length=0, nullable=false)
     */
    private $descriptionGroup;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;

    public function getIdGroup(): ?int
    {
        return $this->idGroup;
    }

    public function getNomGroup(): ?string
    {
        return $this->nomGroup;
    }

    public function setNomGroup(string $nomGroup): static
    {
        $this->nomGroup = $nomGroup;

        return $this;
    }

    public function getDescriptionGroup(): ?string
    {
        return $this->descriptionGroup;
    }

    public function setDescriptionGroup(string $descriptionGroup): static
    {
        $this->descriptionGroup = $descriptionGroup;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }


}
