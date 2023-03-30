<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\InputPayload\Company\UpdateCompanyVisuelInputPayload;
use App\Processor\Company\CompanyVisuelProcessor;
use App\Repository\CompanyRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    shortName: 'Company',
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['company:read'],
            ],
        ),
        new Get(
            normalizationContext: [
                'groups' => ['company:read', 'user:read'],
            ],
        ),
        new Post(status: 202),
        new Put(status: 200),
        new Delete(status: 204),
        new Put(
            '/companies/{id}/visuel{._format}',
            status: 202,
            openapiContext: [
                'summary' => 'update company visuel (logo)',
            ],
            input: UpdateCompanyVisuelInputPayload::class,
            processor: CompanyVisuelProcessor::class
        ),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class Company
{
    use ChangeTrackerTrait;
    use SoftDeletableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'address:read', 'company:read', 'shop:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le siret est requis.')]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le nom est requis.')]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'La description est requise.')]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le numéro de téléphone est requis.')]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private ?string $siteWeb = null;

    #[ORM\ManyToOne]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le secteur d\'activité est requis.')]
    private ?Sector $sector = null;

    #[ORM\ManyToOne]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le type de structure est requis.')]
    private ?StructureType $structureType = null;

    /**
     * @var Collection<int, Address> $addresses
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Address::class)]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private Collection $addresses;

    /**
     * @var Collection<int, Shop> $shops
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Shop::class, orphanRemoval: true)]
    #[Groups(['user:read', 'company:read'])]
    private Collection $shops;

    /**
     * @var Collection<int, Label> $label
     */
    #[ORM\ManyToMany(targetEntity: Label::class, inversedBy: 'companies')]
    #[Groups(['user:read', 'company:read', 'shop:read'])]
    private Collection $label;

    /**
     * @var Collection<int, User> $users
     */
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    #[Groups(['company:read'])]
    private Collection $users;

    // add nulable true
    #[ORM\OneToOne(targetEntity: Attachment::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'attachment_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['user:read', 'company:read'])]
    private ?Attachment $attachment = null;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->shops = new ArrayCollection();
        $this->label = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getStructureType(): ?StructureType
    {
        return $this->structureType;
    }

    public function setStructureType(?StructureType $structureType): self
    {
        $this->structureType = $structureType;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (! $this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCompany($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCompany() === $this) {
                $address->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): self
    {
        if (! $this->shops->contains($shop)) {
            $this->shops->add($shop);
            $shop->setCompany($this);
        }

        return $this;
    }

    public function removeShop(Shop $shop): self
    {
        if ($this->shops->removeElement($shop)) {
            // set the owning side to null (unless already changed)
            if ($shop->getCompany() === $this) {
                $shop->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Label>
     */
    public function getLabel(): Collection
    {
        return $this->label;
    }

    public function addLabel(Label $label): self
    {
        if (! $this->label->contains($label)) {
            $this->label->add($label);
        }

        return $this;
    }

    public function removeLabel(Label $label): self
    {
        $this->label->removeElement($label);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (! $this->users->contains($user)) {
            $this->users->add($user);
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    public function setAttachment(?Attachment $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }
}
