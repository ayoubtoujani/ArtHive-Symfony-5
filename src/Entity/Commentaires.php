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
 * @ORM\ManyToOne(targetEntity=Publications::class)
 * @ORM\JoinColumn(name="id_publication", referencedColumnName="id_publication")
 */
private $publication;

/**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;

public function getUser(): ?Users
    {
        return $this->user;
    }
    public function setUser(?Users $users): self
    {
        $this->user = $users;
        return $this;
    }
    public function getPublication(): ?Publications
    {
        return $this->publication;
    }
    public function setPublication(?Publications $publications): self
    {
        $this->publication = $publications;
        return $this;
    }
   

    public function getIdCommentaire(): ?int
    {
        return $this->idCommentaire;
    }

    public function setIdCommentaire(int $idCommentaire): static
    {
        $this->idCommentaire = $idCommentaire;

        return $this;
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

}
