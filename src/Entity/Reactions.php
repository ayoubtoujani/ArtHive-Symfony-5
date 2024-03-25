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
     * @ORM\Column(name="id_reaction", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReaction;

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
     * @var \DateTime
     *
     * @ORM\Column(name="d_ajout_reaction", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dAjoutReaction = 'CURRENT_TIMESTAMP';

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

    public function setPublication(?Publications $publications): self
    {
        $this->publication = $publications;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $users): self
    {
        $this->user = $users;
        return $this;
    }

    public function getDAjoutReaction(): ?\DateTimeInterface
    {
        return $this->dAjoutReaction;
    }

    public function setDAjoutReaction(\DateTimeInterface $dAjoutReaction): static
    {
        $this->dAjoutReaction = $dAjoutReaction;

        return $this;
    }


}
