<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * reclamationgroupe
 *
 * @ORM\Table(name="reclamationgroupe",  indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 */
class Reclamationgroupe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_reclamation", type="integer", nullable=false)
     */
    private $idReclamation;

    /**
     * @ORM\Column(name="descReclamation", type="text", length=255)
     * @Assert\Length(
     *       min = 10,
     *       max = 255,
     *       minMessage = "descReclamation  must be at least {{ limit }} characters long",
     *       maxMessage = "descReclamation  cannot be longer than {{ limit }} characters"
     *  )
     * /
     * @Assert\NotBlank(message="Nom descReclamation cannot be blank")
     */
    private $descReclamation;

    /**
     * @ORM\ManyToOne(targetEntity=Groups::class)
     * @ORM\JoinColumn(name="id_groupe", referencedColumnName="id_group")
     */
    private $group;

    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }

    public function setIdReclamation(?int $idReclamation): void
    {
        $this->idReclamation = $idReclamation;
    }

    public function setGroupId(?Groups $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): ?Groups
    {
        return $this->group;
    }

    public function setGroup(?Groups $group): void
    {
        $this->group = $group;
    }

    public function getDescReclamation(): ?string
    {
        return $this->descReclamation;
    }

    public function setDescReclamation(?string $descReclamation): void
    {
        $this->descReclamation = $descReclamation;
    }
}
