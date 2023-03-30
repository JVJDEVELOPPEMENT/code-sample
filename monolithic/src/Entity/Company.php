<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le nom est requis.")]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotNull(message: "Le site web est requis.")]
    private ?string $website = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "La ville est requise.")]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "La rue est requise.")]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le pays est requis.")]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "La taille de l'entreprise est requise.")]
    private ?string $employeeSize = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Le secteur est requis.")]
    private ?string $sector = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $foundedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getEmployeeSize(): ?string
    {
        return $this->employeeSize;
    }

    public function setEmployeeSize(string $employeeSize): self
    {
        $this->employeeSize = $employeeSize;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getFoundedAt(): ?\DateTimeImmutable
    {
        return $this->foundedAt;
    }

    public function setFoundedAt(?\DateTimeImmutable $foundedAt): self
    {
        $this->foundedAt = $foundedAt;

        return $this;
    }
}
