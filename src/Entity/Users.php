<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 * @ORM\Entity
 * @ORM\Table(name="users", indexes={@ORM\Index(name="nom_user", columns={"nom_user"})})
 */

class Users
{

/**
     * @ORM\Id
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUser;

    /**
     * @ORM\Column(name="nom_user", type="string", length=255, nullable=false)
     */
    private $nomUser;

    /**
     * @ORM\Column(name="prenom_user", type="string", length=255, nullable=false)
     */
    private $prenomUser;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(name="mdp_user", type="string", length=255, nullable=false)
     */
    private $mdpUser;

    /**
     * @ORM\Column(name="d_naissance_user", type="date", nullable=false)
     */
    private $dNaissanceUser;

    /**
     * @ORM\Column(name="ville", type="string", length=255, nullable=false)
     */
    private $ville;

    /**
     * @ORM\Column(name="num_tel_user", type="string", length=255, nullable=false)
     */
    private $numTelUser;

    /**
     * @ORM\Column(name="photo", type="text", length=0, nullable=false)
     */
    private $photo;

    /**
     * @ORM\Column(name="role", type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(name="bio", type="text", length=0, nullable=false)
     */
    private $bio;

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getNomUser(): ?string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): static
    {
        $this->nomUser = $nomUser;

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenomUser;
    }

    public function setPrenomUser(string $prenomUser): static
    {
        $this->prenomUser = $prenomUser;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdpUser(): ?string
    {
        return $this->mdpUser;
    }

    public function setMdpUser(string $mdpUser): static
    {
        $this->mdpUser = $mdpUser;

        return $this;
    }

    public function getDNaissanceUser(): ?\DateTimeInterface
    {
        return $this->dNaissanceUser;
    }

    public function setDNaissanceUser(\DateTimeInterface $dNaissanceUser): static
    {
        $this->dNaissanceUser = $dNaissanceUser;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getNumTelUser(): ?string
    {
        return $this->numTelUser;
    }

    public function setNumTelUser(string $numTelUser): static
    {
        $this->numTelUser = $numTelUser;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }


}
