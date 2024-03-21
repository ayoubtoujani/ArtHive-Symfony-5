<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Messages
 *
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="fk_message", columns={"sender_id_user"}), @ORM\Index(name="fk_message2", columns={"receiver_id_user"})})
 * @ORM\Entity
 */
class Messages
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_message", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMessage;

    /**
     * @var int
     *
     * @ORM\Column(name="sender_id_user", type="integer", nullable=false)
     */
    private $senderIdUser;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu_message", type="text", length=0, nullable=false)
     */
    private $contenuMessage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="d_envoi_message", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dEnvoiMessage = 'CURRENT_TIMESTAMP';

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="receiver_id_user", referencedColumnName="id_user")
     * })
     */
    private $receiverIdUser;

    public function getIdMessage(): ?int
    {
        return $this->idMessage;
    }

    public function getSenderIdUser(): ?int
    {
        return $this->senderIdUser;
    }

    public function setSenderIdUser(int $senderIdUser): static
    {
        $this->senderIdUser = $senderIdUser;

        return $this;
    }

    public function getContenuMessage(): ?string
    {
        return $this->contenuMessage;
    }

    public function setContenuMessage(string $contenuMessage): static
    {
        $this->contenuMessage = $contenuMessage;

        return $this;
    }

    public function getDEnvoiMessage(): ?\DateTimeInterface
    {
        return $this->dEnvoiMessage;
    }

    public function setDEnvoiMessage(\DateTimeInterface $dEnvoiMessage): static
    {
        $this->dEnvoiMessage = $dEnvoiMessage;

        return $this;
    }

    public function getReceiverIdUser(): ?Users
    {
        return $this->receiverIdUser;
    }

    public function setReceiverIdUser(?Users $receiverIdUser): static
    {
        $this->receiverIdUser = $receiverIdUser;

        return $this;
    }


}
