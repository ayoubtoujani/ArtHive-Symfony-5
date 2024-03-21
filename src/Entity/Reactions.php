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
     * @var \DateTime
     *
     * @ORM\Column(name="d_ajout_reaction", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dAjoutReaction = 'CURRENT_TIMESTAMP';

    public function getIdReaction(): ?int
    {
        return $this->idReaction;
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
