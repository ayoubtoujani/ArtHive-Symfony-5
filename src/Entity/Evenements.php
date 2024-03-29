<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users;

/**
 * Evenements
 *
 * @ORM\Table(name="evenements", indexes={@ORM\Index(name="fk_evenement", columns={"id_user"})})
 * @ORM\Entity
 */
class Evenements
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_evenement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="titre_evenement", type="text", length=0, nullable=false)
     */
    private $titreEvenement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_debut_evenement", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dDebutEvenement = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_fin_evenement", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dFinEvenement = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="description_evenement", type="text", length=0, nullable=false)
     */
    private $descriptionEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu_evenement", type="text", length=0, nullable=false)
     */
    private $lieuEvenement;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text", length=0, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="categorieEvenement", type="string", length=255, nullable=false)
     */
    private $categorieevenement;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function getTitreEvenement(): ?string
    {
        return $this->titreEvenement;
    }

    public function setTitreEvenement(string $titreEvenement): static
    {
        $this->titreEvenement = $titreEvenement;

        return $this;
    }

    public function getDDebutEvenement(): ?\DateTimeInterface
    {
        return $this->dDebutEvenement;
    }

    public function setDDebutEvenement(\DateTimeInterface $dDebutEvenement): static
    {
        $this->dDebutEvenement = $dDebutEvenement;

        return $this;
    }

    public function getDFinEvenement(): ?\DateTimeInterface
    {
        return $this->dFinEvenement;
    }

    public function setDFinEvenement(\DateTimeInterface $dFinEvenement): static
    {
        $this->dFinEvenement = $dFinEvenement;

        return $this;
    }

    public function getDescriptionEvenement(): ?string
    {
        return $this->descriptionEvenement;
    }

    public function setDescriptionEvenement(string $descriptionEvenement): static
    {
        $this->descriptionEvenement = $descriptionEvenement;

        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieuEvenement;
    }

    public function setLieuEvenement(string $lieuEvenement): static
    {
        $this->lieuEvenement = $lieuEvenement;

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

    public function getCategorieevenement(): ?string
    {
        return $this->categorieevenement;
    }

    public function setCategorieevenement(string $categorieevenement): static
    {
        $this->categorieevenement = $categorieevenement;

        return $this;
    }

   /* public function getIdUser(): ?Users
    {
        return $this->idUser;
    }*/

    public function setIdUser(?Users $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }


}
