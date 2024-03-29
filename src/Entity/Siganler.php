<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Siganler
 *
 * @ORM\Table(name="siganler", indexes={@ORM\Index(name="fk_signaler2", columns={"id_publication"})})
 * @ORM\Entity
 */
class Siganler
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_publication", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idPublication;

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }


}
