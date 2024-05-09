<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users;


/**
 * Produits
 *
 * @ORM\Table(name="produits", indexes={@ORM\Index(name="fk_produit1", columns={"id_user"})})
 * @ORM\Entity
 */
class Produits
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_produit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProduit;

    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_produit", type="string", length=255, nullable=false)
     *
     */
    private $nomProduit;

    /**
     * @var string
     *
     * @ORM\Column(name="image_produit", type="string", length=255, nullable=false)
     */
    private $imageProduit;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_produit", type="float", precision=10, scale=0, nullable=false)
     */
    private $prixProduit;

    /**
     * @var string
     *
     * @ORM\Column(name="description_produit", type="string", length=255, nullable=false)
     */
    private $descriptionProduit;

    /**
     * @var bool
     *
     * @ORM\Column(name="disponibilite", type="boolean", nullable=false)
     */
    private $disponibilite;

    /**
     * @var string
     *
     * @ORM\Column(name="categ_produit", type="string", length=255, nullable=false)
     *
     */
    private $categProduit;

    /**
     * @var int
     *
     * @ORM\Column(name="stock_produit", type="integer", nullable=false)
     */
    private $stockProduit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_publication_produit", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dPublicationProduit = 'CURRENT_TIMESTAMP';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    private $user;


    public function setUser(?Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    
    

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit=null): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getImageProduit(): ?string
    {
        return $this->imageProduit;
    }

    public function setImageProduit(string $imageProduit): static
    {
        $this->imageProduit = $imageProduit;

        return $this;
    }

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit=null): self
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getDescriptionProduit(): ?string
    {
        return $this->descriptionProduit;
    }

    public function setDescriptionProduit(string $descriptionProduit=null): self
    {
        $this->descriptionProduit = $descriptionProduit;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getCategProduit(): ?string
    {
        return $this->categProduit;
    }

    public function setCategProduit(string $categProduit=null): self
    {
        $this->categProduit = $categProduit;

        return $this;
    }

    public function getStockProduit(): ?int
    {
        return $this->stockProduit;
    }

    public function setStockProduit(int $stockProduit=null): self
    {
        $this->stockProduit = $stockProduit;

        return $this;
    }

    public function getDPublicationProduit(): ?\DateTimeInterface
    {
        return $this->dPublicationProduit;
    }

    public function setDPublicationProduit(\DateTimeInterface $dPublicationProduit): static
    {
        $this->dPublicationProduit = $dPublicationProduit;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function __construct()
    {
        $this->dPublicationProduit = new \DateTime();
    }


}
