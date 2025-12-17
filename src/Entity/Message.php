<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $sender = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $recipient = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le sujet est obligatoire")]
    #[Assert\Length(
        max: 20,
        maxMessage: "Le sujet ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Le contenu du message est obligatoire")]
    private ?string $content = null;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $isRead = false;

    #[ORM\Column(options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $deletedBySender = false;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $deletedByRecipient = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isRead = false;
        $this->deletedBySender = false;
        $this->deletedByRecipient = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): static
    {
        $this->sender = $sender;
        return $this;
    }

    public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): static
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isDeletedBySender(): ?bool
    {
        return $this->deletedBySender;
    }

    public function setDeletedBySender(bool $deletedBySender): static
    {
        $this->deletedBySender = $deletedBySender;
        return $this;
    }

    public function isDeletedByRecipient(): ?bool
    {
        return $this->deletedByRecipient;
    }

    public function setDeletedByRecipient(bool $deletedByRecipient): static
    {
        $this->deletedByRecipient = $deletedByRecipient;
        return $this;
    }
}



