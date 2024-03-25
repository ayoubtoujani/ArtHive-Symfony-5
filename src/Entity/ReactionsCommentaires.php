<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReactionsCommentaires
 *
 * @ORM\Table(name="reactions_commentaires", indexes={@ORM\Index(name="fk_reaction_commentaire1", columns={"id_user"}), @ORM\Index(name="fk_reaction_commentaire2", columns={"id_publication"}), @ORM\Index(name="fk_reaction_commentaire3", columns={"id_commentaire"})})
 * @ORM\Entity
 */
class ReactionsCommentaires
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reaction_commentaire", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReactionCommentaire;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity=Publications::class)
     * @ORM\JoinColumn(name="id_publication", referencedColumnName="id_publication")
     */
    private $publication;

    /**
     * @ORM\ManyToOne(targetEntity=Commentaires::class)
     * @ORM\JoinColumn(name="id_commentaire", referencedColumnName="id_commentaire")
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_ajout_reaction_commentaire", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dAjoutReactionCommentaire = 'CURRENT_TIMESTAMP';

    public function getIdReactionCommentaire(): ?int
    {
        return $this->idReactionCommentaire;
    }

    public function setIdReactionCommentaire(int $idReactionCommentaire): static
    {
        $this->idReactionCommentaire = $idReactionCommentaire;

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
   
    public function getPublication(): ?Publications
    {
        return $this->publication;
    }

    public function setPublication(?Publications $publication): static
    {
        $this->publication = $publication;

        return $this;
    }

    public function getCommentaire(): ?Commentaires
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaires $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDAjoutReactionCommentaire(): ?\DateTimeInterface
    {
        return $this->dAjoutReactionCommentaire;
    }

    public function setDAjoutReactionCommentaire(\DateTimeInterface $dAjoutReactionCommentaire): static
    {
        $this->dAjoutReactionCommentaire = $dAjoutReactionCommentaire;

        return $this;
    }


}
