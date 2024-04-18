<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users; // Import the Users entity class
use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="publications", indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_publication", type="integer")
     */
    private $idPublication;

    /**
     * @var string
     * @Assert\NotBlank(message="The Content of the Post should not be empty")
     * @ORM\Column(name="contenu_publication", type="text", length=16777215)
     */
    private $contenuPublication;

    /**
     * @ORM\Column(name="d_creation_publication", type="datetime")
     */
    private $dCreationPublication;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    private $user;

     /**
     * @ORM\Column(name="url_file", type="text", length=255, nullable=false)
     */
    private $urlFile;

     /**
     * @ORM\ManyToMany(targetEntity=Users::class , cascade={"persist"})
     * @ORM\JoinTable(name="publication_user_favorite",
     *      joinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id_publication")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id_user")}
     * )
     */
    private $favoriteUsers;
    public function __construct()
    {
        $this->favoriteUsers = new ArrayCollection();
    }

    public function getFavoriteUsers(): Collection
    {
        return $this->favoriteUsers;
    }

    public function addFavoriteUser(Users $user): self
    {
        if (!$this->favoriteUsers->contains($user)) {
            $this->favoriteUsers[] = $user;
        }

        return $this;
    }

    public function removeFavoriteUser(Users $user): self
    {
        $this->favoriteUsers->removeElement($user);

        return $this;
    }

    // Getter and setter methods for other properties...

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdPublication(): ?int
    {
        return $this->idPublication;
    }

    public function setIdPublication(int $idPublication): self
    {
        $this->idPublication = $idPublication;
        return $this;
    }

    public function getContenuPublication(): ?string
    {
        return $this->contenuPublication;
    }

    public function setContenuPublication(string $contenuPublication): self
    {
        $this->contenuPublication = $contenuPublication;
        return $this;
    }

    public function getDCreationPublication(): ?\DateTimeInterface
    {
        return $this->dCreationPublication;
    }

    public function setDCreationPublication(\DateTimeInterface $dCreationPublication): self
    {
        $this->dCreationPublication = $dCreationPublication;
        return $this;
    }



    public function getUrlFile(): ?string
    {
        return $this->urlFile;
    }

    public function setUrlFile(string $urlFile=null): self
    {
        $this->urlFile = $urlFile;
        return $this;
    }
   /**
     * @ORM\OneToMany(targetEntity=Reactions::class , mappedBy="publication")
     * @ORM\JoinColumn(name="id_publication", referencedColumnName="id_publication")
     */
    private $reactions;

    /**
     * @return Collection|Reactions[]
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    //count all reactions
    public function countReactions(): int
    {
        return count($this->reactions);
    }
    

}
