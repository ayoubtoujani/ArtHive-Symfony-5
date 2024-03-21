<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PublicationRepository;


/**
 * Publications
 *
 * @ORM\Table(name="publications", indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 */
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publications
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_publication", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPublication;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu_publication", type="text", length=16777215, nullable=false)
     */
    private $contenuPublication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_creation_publication", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dCreationPublication = 'CURRENT_TIMESTAMP';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url_file", type="text", length=0, nullable=true)
     */
    private $urlFile;

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }

    public function getContenuPublication(): ?string
    {
        return $this->contenuPublication;
    }

    public function setContenuPublication(string $contenuPublication): static
    {
        $this->contenuPublication = $contenuPublication;

        return $this;
    }

    public function getDCreationPublication(): ?\DateTimeInterface
    {
        return $this->dCreationPublication;
    }

    public function setDCreationPublication(\DateTimeInterface $dCreationPublication): static
    {
        $this->dCreationPublication = $dCreationPublication;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(?int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getUrlFile(): ?string
    {
        return $this->urlFile;
    }

    public function setUrlFile(?string $urlFile): static
    {
        $this->urlFile = $urlFile;

        return $this;
    }


}
