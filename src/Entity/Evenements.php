<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use DateTime; // Importation de la classe DateTime


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
         * @Assert\NotBlank(message=" titre doit etre non vide")
          * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Le titre ne doit pas contenir de chiffres"
     * )
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un titre au mini de 5 caracteres"
     *
     *     )
     * @ORM\Column(name="titre_evenement", type="text", length=0, nullable=false)
     */
    private $titreEvenement;
    /**
 * @Assert\NotBlank(message="La date de début de l'événement est requise")
 */

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="La date de début doit étre non vide")
     * @ORM\Column(name="d_debut_evenement", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dDebutEvenement ;

    /**
     * @var \DateTime
     *
        * @Assert\NotBlank(message="La date de fin doit étre non vide")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="dDebutEvenement",
     *     message="La date de fin doit être postérieure ou égale à la date de début"
     * )
     * @ORM\Column(name="d_fin_evenement", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dFinEvenement ;

    /**
     * @var string
     *
      * @Assert\NotBlank(message="description  doit etre non vide")
     * @Assert\Length(
     *      min = 7,
     *      max = 10000,
     *      minMessage = "doit etre >=7 ",
     *      maxMessage = "doit etre <=10000" )
     * @ORM\Column(name="description_evenement", type="text", length=0, nullable=false)
     */
    private $descriptionEvenement;

    /**
     * @var string
           * @Assert\NotBlank(message="lieu  doit etre non vide")

     * @ORM\Column(name="lieu_evenement", type="text", length=0, nullable=false)
     */
    private $lieuEvenement;

    /**
     * @var string
           * @Assert\NotBlank(message="image  doit etre non vide")

     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var string
           * @Assert\NotBlank(message="categorie  doit etre non vide")

     * @ORM\Column(name="categorieEvenement", type="string", length=0, nullable=false)
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

    public function setTitreEvenement(string $titreEvenement = null): self
    {
        $this->titreEvenement = $titreEvenement;

        return $this;
    }

    public function getDDebutEvenement(): ?\DateTimeInterface
    {
        return $this->dDebutEvenement;
    }

    public function setDDebutEvenement(\DateTimeInterface $dDebutEvenement = null): self
    {
        $this->dDebutEvenement = $dDebutEvenement;

        return $this;
    }

    public function getDFinEvenement(): ?\DateTimeInterface
    {
        return $this->dFinEvenement;
    }

    public function setDFinEvenement(\DateTimeInterface $dFinEvenement = null): self
    {
        $this->dFinEvenement = $dFinEvenement;

        return $this;
    }

    public function getDescriptionEvenement(): ?string
    {
        return $this->descriptionEvenement;
    }

    public function setDescriptionEvenement(string $descriptionEvenement = null): self
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

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
/**
 * @return int
 */
public function getParticipantsCount(): int
{
    $participations = $this->getParticipationsCount();
    return $participations->count();
}


 /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="idEvenement")
     */
    private $participations;

    // ...

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }


     /**
     * Vérifie si la date de l'événement est passée.
     *
     * @return bool
     */
    public function isDatePassed(): bool
    {
        $currentDate = new DateTime();
        return $this->dFinEvenement < $currentDate;
    }

    
}
