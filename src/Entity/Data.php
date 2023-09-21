<?php

namespace App\Entity;

use App\Repository\DataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataRepository::class)]
class Data
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $id;

    #[ORM\Column(length: 255)]
    private ?string $purchaseDate;

    #[ORM\Column(length: 255)]
    private ?string $shipToName;

    #[ORM\Column(length: 255)]
    private ?string $customerEmail;

    #[ORM\Column(length: 255)]
    private ?string $grantTotal ;

    #[ORM\Column(length: 255)]
    private ?string $status;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPurchaseDate(): ?string
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(string $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getShipToName(): ?string
    {
        return $this->shipToName;
    }

    public function setShipToName(string $shipToName): static
    {
        $this->shipToName = $shipToName;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): static
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getGrantTotal(): ?string
    {
        return $this->grantTotal;
    }

    public function setGrantTotal(string $grantTotal): static
    {
        $this->grantTotal = $grantTotal;

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
}
