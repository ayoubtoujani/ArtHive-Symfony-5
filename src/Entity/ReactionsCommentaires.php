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
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_publication", type="integer", nullable=false)
     */
    private $idPublication;

    /**
     * @var int
     *
     * @ORM\Column(name="id_commentaire", type="integer", nullable=false)
     */
    private $idCommentaire;

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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }

    public function setIdPublication(int $idPublication): static
    {
        $this->idPublication = $idPublication;

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
