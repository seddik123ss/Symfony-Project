<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Sale::class, inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Sale $sale = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du client est obligatoire.')]
    private ?string $clientName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'email du client est obligatoire.')]
    #[Assert\Email(message: 'L\'email n\'est pas valide.')]
    private ?string $clientEmail = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminMessage = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'float', message: 'Le montant offert doit être un nombre.')]
    #[Assert\GreaterThan(value: 0, message: 'Le montant offert doit être supérieur à 0.')]
    private ?float $offeredAmount = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: 'float', message: 'Le montant du vendeur doit être un nombre.')]
    #[Assert\GreaterThan(value: 0, message: 'Le montant du vendeur doit être supérieur à 0.')]
    private ?float $vendorAmount = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $clientAccepted = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $vendorAccepted = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isFinalOffer = false;

    public function __construct()
    {
        $this->clientAccepted = false;
        $this->vendorAccepted = false;
        $this->isFinalOffer = false;
        $this->status = 'pending';
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): static
    {
        $this->sale = $sale;
        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): static
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): static
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getAdminMessage(): ?string
    {
        return $this->adminMessage;
    }

    public function setAdminMessage(?string $adminMessage): static
    {
        $this->adminMessage = $adminMessage;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getOfferedAmount(): ?float
    {
        return $this->offeredAmount;
    }

    public function setOfferedAmount(?float $offeredAmount): static
    {
        $this->offeredAmount = $offeredAmount;
        return $this;
    }

    public function getVendorAmount(): ?float
    {
        return $this->vendorAmount;
    }

    public function setVendorAmount(?float $vendorAmount): static
    {
        $this->vendorAmount = $vendorAmount;
        return $this;
    }

    public function isClientAccepted(): ?bool
    {
        return $this->clientAccepted;
    }

    public function setClientAccepted(bool $clientAccepted): static
    {
        $this->clientAccepted = $clientAccepted;
        return $this;
    }

    public function isVendorAccepted(): ?bool
    {
        return $this->vendorAccepted;
    }

    public function setVendorAccepted(bool $vendorAccepted): static
    {
        $this->vendorAccepted = $vendorAccepted;
        return $this;
    }

    public function isFinalOffer(): ?bool
    {
        return $this->isFinalOffer;
    }

    public function setIsFinalOffer(bool $isFinalOffer): static
    {
        $this->isFinalOffer = $isFinalOffer;
        return $this;
    }

    /**
     * Vérifie si l'offre est mutuellement acceptée
     */
    public function isMutuallyAccepted(): bool
    {
        return $this->clientAccepted && $this->vendorAccepted;
    }

    /**
     * Vérifie si l'offre peut être modifiée
     */
    public function canBeModified(): bool
    {
        return !$this->isFinalOffer && $this->status === 'pending';
    }
}

