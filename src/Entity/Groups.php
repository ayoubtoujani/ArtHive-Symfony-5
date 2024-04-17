<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GroupsRepository::class)
 *
 *
 */
class Groups
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     */
    private $idGroup;

    /**
     * @ORM\Column(name="nom_group", type="text", length=255)
     * @Assert\NotBlank(message="Nom group cannot be blank")
     * @Assert\Length(
     *       min = 2,
     *       max = 255,
     *       minMessage = "Nom group must be at least {{ limit }} characters long",
     *       maxMessage = "Nom group cannot be longer than {{ limit }} characters"
     *  )
     * /
     */
    private $nomGroup;

    /**
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank(message="Image cannot be blank")
     */
    private $image;

    /**
     * @ORM\Column(name="description_group", type="text", length=255)
     * @Assert\NotBlank(message="Description group cannot be blank")
     */
    private $descriptionGroup;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @Assert\Length(
     *       min = 10,
     *       max = 255,
     *       minMessage = "Description group must be at least {{ limit }} characters long",
     *       maxMessage = "Description group cannot be longer than {{ limit }} characters"
     *  )
     * /
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Reclamationgroupe::class, mappedBy="idGroup")
     */
    private $reclamationgroupes;

    public function __construct()
    {
        $this->reclamationgroupes = new ArrayCollection();
    }

    public function getIdGroup(): ?int
    {
        return $this->idGroup;
    }

    public function getNomGroup(): ?string
    {
        return $this->nomGroup;
    }

    public function setNomGroup(string $nomGroup): self
    {
        $this->nomGroup = $nomGroup;
        return $this;
    }

    public function getDescriptionGroup(): ?string
    {
        return $this->descriptionGroup;
    }

    public function setDescriptionGroup(string $descriptionGroup): self
    {
        $this->descriptionGroup = $descriptionGroup;
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
    public function __toString(): string
    {
        return $this->nomGroup ?? '';
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Reclamationgroupe>
     */
    public function getReclamationgroupes(): Collection
    {
        return $this->reclamationgroupes;
    }

    public function addReclamationgroupe(Reclamationgroupe $reclamationgroupe): self
    {
        if (!$this->reclamationgroupes->contains($reclamationgroupe)) {
            $this->reclamationgroupes->add($reclamationgroupe);
            $reclamationgroupe->setIdGroup($this);
        }

        return $this;
    }

    public function removeReclamationgroupe(Reclamationgroupe $reclamationgroupe): self
    {
        if ($this->reclamationgroupes->removeElement($reclamationgroupe)) {
            // set the owning side to null (unless already changed)
            if ($reclamationgroupe->getIdGroup() === $this) {
                $reclamationgroupe->setIdGroup(null);
            }
        }

        return $this;
    }
}