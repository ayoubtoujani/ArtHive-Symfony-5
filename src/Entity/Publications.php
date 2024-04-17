<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users; // Import the Users entity class
/**
 * @ORM\Table(name="publications", indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_publication", type="integer")
     */
    private $idPublication;

    /**
     * @ORM\Column(name="contenu_publication", type="text", length=16777215)
     */
    private $contenuPublication;

    /**
     * @ORM\Column(name="d_creation_publication", type="datetime")
     */
    private $dCreationPublication;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="url_file", type="text", length=0, nullable=true)
     */
    private $urlFile;

    // Getter and setter methods for other properties...

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }

    public function setIdPublication(int $idPublication): self
    {
        $this->idPublication = $idPublication;
        return $this;
    }

    public function getContenuPublication(): ?string
    {
        return $this->contenuPublication;
    }

    public function setContenuPublication(string $contenuPublication): self
    {
        $this->contenuPublication = $contenuPublication;
        return $this;
    }

    public function getDCreationPublication(): ?\DateTimeInterface
    {
        return $this->dCreationPublication;
    }

    public function setDCreationPublication(\DateTimeInterface $dCreationPublication): self
    {
        $this->dCreationPublication = $dCreationPublication;
        return $this;
    }



    public function getUrlFile(): ?string
    {
        return $this->urlFile;
    }

    public function setUrlFile(?string $urlFile): self
    {
        $this->urlFile = $urlFile;
        return $this;
    }
}
