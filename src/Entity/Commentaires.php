<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaires
 *
 * @ORM\Table(name="commentaires", indexes={@ORM\Index(name="fk_commentaires1", columns={"id_publication"}), @ORM\Index(name="fk_commentaires2", columns={"id_user"})})
 * @ORM\Entity
 */
class Commentaires
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_commentaire", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCommentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu_commentaire", type="text", length=0, nullable=false)
     */
    private $contenuCommentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_ajout_commentaire", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dAjoutCommentaire = 'CURRENT_TIMESTAMP';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_publication", type="integer", nullable=true)
     */
    private $idPublication;

    public function getIdCommentaire(): ?int
    {
        return $this->idCommentaire;
    }

    public function getContenuCommentaire(): ?string
    {
        return $this->contenuCommentaire;
    }

    public function setContenuCommentaire(string $contenuCommentaire): static
    {
        $this->contenuCommentaire = $contenuCommentaire;

        return $this;
    }

    public function getDAjoutCommentaire(): ?\DateTimeInterface
    {
        return $this->dAjoutCommentaire;
    }

    public function setDAjoutCommentaire(\DateTimeInterface $dAjoutCommentaire): static
    {
        $this->dAjoutCommentaire = $dAjoutCommentaire;

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

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }

    public function setIdPublication(?int $idPublication): static
    {
        $this->idPublication = $idPublication;

        return $this;
    }


}
