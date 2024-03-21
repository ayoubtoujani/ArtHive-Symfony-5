<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="description_group", type="text", length=0, nullable=false)
     */
    private $descriptionGroup;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;

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

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }


}
