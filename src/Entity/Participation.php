<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation", indexes={@ORM\Index(name="FK_EV", columns={"id_evenement"}), @ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 */

class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_participation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idParticipation;

 
    /**
 * @var \Users
 *
 * @ORM\ManyToOne(targetEntity="Users", cascade={"persist"})
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
 * })
 */
    private $idUser;

    /**
     * @var \Evenements
     *
     * @ORM\ManyToOne(targetEntity="Evenements")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evenement", referencedColumnName="id_evenement")
     * })
     */
    private $idEvenement;

    public function getIdParticipation(): ?int
    {
        return $this->idParticipation;
    }

    public function getIdUser(): ?Users
    {
        return $this->idUser;
    }

    public function setIdUser(?Users $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdEvenement(): ?Evenements
    {
        return $this->idEvenement;
    }

    public function setIdEvenement(?Evenements $idEvenement): static
    {
        $this->idEvenement = $idEvenement;

        return $this;
    }


}
