<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reactions
 *
 * @ORM\Table(name="reactions", indexes={@ORM\Index(name="fk_reaction1", columns={"id_user"}), @ORM\Index(name="fk_reaction2", columns={"id_publication"})})
 * @ORM\Entity
 */
class Reactions
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_reaction", type="integer")
     */
    private $idReaction;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class )
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Publications::class )
     * @ORM\JoinColumn(name="id_publication", referencedColumnName="id_publication")
     */
    private $publication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_ajout_reaction", type="datetime", nullable=false)
     */
    private $dAjoutReaction;

    public function getIdReaction(): ?int
    {
        return $this->idReaction;
    }

    public function setIdReaction(int $idReaction): self
    {
        $this->idReaction = $idReaction;

        return $this;
    }

    public function getPublication(): ?Publications
    {
        return $this->publication;
    }

    public function setPublication(?Publications $publication): self
    {
        $this->publication = $publication;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDAjoutReaction(): ?\DateTimeInterface
    {
        return $this->dAjoutReaction;
    }

    public function setDAjoutReaction(\DateTimeInterface $dAjoutReaction): self
    {
        $this->dAjoutReaction = $dAjoutReaction;

        return $this;
    }
}
