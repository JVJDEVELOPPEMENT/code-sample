<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\MeController;
use App\InputPayload\User\AddTeamMemberInputPayload;
use App\InputPayload\User\ChangePasswordInputPayload;
use App\OutputPayload\User\ActivateUserResponse;
use App\OutputPayload\User\AddTeamMemberOutputPayload;
use App\OutputPayload\User\BlockUserResponse;
use App\OutputPayload\User\ChangePasswordResponse;
use App\Processor\User\ActivateUserProcessor;
use App\Processor\User\AddTeamMemberProcessor;
use App\Processor\User\BlockUserProcessor;
use App\Processor\User\CreateUserProcessor;
use App\Processor\User\UserPasswordUpdateProcessor;
use App\Repository\UserRepository;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    shortName: 'User',
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['user:read'],
            ],
        ),
        new GetCollection(
            name: 'me',
            controller: MeController::class,
            uriTemplate: '/users/me',
            read: true,
            normalizationContext: [
                'groups' => ['user:read'],
            ],
        ),
        new Get(
            normalizationContext: [
                'groups' => ['user:read'],
            ],
        ),
        new Post(status: 202),
        new Put(status: 200),
        new Delete(status: 204),
        new Put(
            '/users/{id}/block{._format}',
            status: 202,
            openapiContext: [
                'summary' => 'block user',
            ],
            output: BlockUserResponse::class,
            processor: BlockUserProcessor::class
        ),
        new Put(
            '/users/{id}/activate{._format}',
            status: 202,
            openapiContext: [
                'summary' => 'activate user',
            ],
            output: ActivateUserResponse::class,
            processor: ActivateUserProcessor::class
        ),
        new Put(
            '/users/{id}/update-password{._format}',
            status: 202,
            openapiContext: [
                'summary' => 'update user password',
            ],
            input: ChangePasswordInputPayload::class,
            output: ChangePasswordResponse::class,
            processor: UserPasswordUpdateProcessor::class
        ),
        new Post(
            uriTemplate: '/users/register.{_format}',
            status: 202,
            openapiContext: [
                'summary' => 'register new user',
            ],
            processor: CreateUserProcessor::class
        ),
        new Post(
            uriTemplate: '/users/add-team-member.{_format}',
            status: 202,
            openapiContext: [
                'summary' => 'add a team member',
            ],
            input: AddTeamMemberInputPayload::class,
            output: AddTeamMemberOutputPayload::class,
            processor: AddTeamMemberProcessor::class
        ),
    ]
)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse email.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SoftDeletableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'company:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotNull(message: 'L\'email est requis.')]
    #[Groups(['user:read', 'company:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le mot de passe est requis.')]
    #[Groups(['user:read', 'company:read'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le prénom est requis.')]
    #[Groups(['user:read', 'company:read'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le nom de famille est requis.')]
    #[Groups(['user:read', 'company:read'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'Le numéro de téléphone est requis.')]
    #[Groups(['user:read', 'company:read'])]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(['user:read', 'company:read'])]
    private ?bool $emailValid = false;

    #[ORM\Column]
    #[Groups(['user:read', 'company:read'])]
    private ?bool $isActive = true;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La sélection des cgu doit être renseignée.')]
    #[Groups(['user:read'])]
    private ?bool $cguAccepted = true;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La sélection des cgv doit être renseignée.')]
    #[Groups(['user:read'])]
    private ?bool $cgvAccepted = true;

    /**
     * @var Collection<int, Role> $roleMember
     */
    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[Groups(['user:read', 'company:read'])]
    private Collection $roleMember;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?Company $company = null;

    public function __construct()
    {
        $this->roleMember = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        return;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function isEmailValid(): ?bool
    {
        return $this->emailValid;
    }

    public function setEmailValid(bool $emailValid): self
    {
        $this->emailValid = $emailValid;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    #[Groups(['user:read', 'company:read'])]
    public function getDisplayName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getRoles(): array
    {
        //TODO , actually send real roles
         // return ['store_owner'];
        return ['super_admin'];
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoleMember(): Collection
    {
        return $this->roleMember;
    }

    public function addRoleMember(Role $roleMember): self
    {
        if (! $this->roleMember->contains($roleMember)) {
            $this->roleMember->add($roleMember);
        }

        return $this;
    }

    public function removeRoleMember(Role $roleMember): self
    {
        $this->roleMember->removeElement($roleMember);

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function isCguAccepted(): ?bool
    {
        return $this->cguAccepted;
    }

    public function setCguAccepted(bool $cguAccepted): self
    {
        $this->cguAccepted = $cguAccepted;

        return $this;
    }

    public function isCgvAccepted(): ?bool
    {
        return $this->cgvAccepted;
    }

    public function setCgvAccepted(bool $cgvAccepted): self
    {
        $this->cgvAccepted = $cgvAccepted;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getUserInformations(): array
    {
        return [
            'roleMembers' => array_map(fn ($role) => $role->getName(), $this->getRoleMember()->toArray()),
            'isEmailValid' => $this->isEmailValid(),
            'isDeleted' => $this->isDeleted(),
            'isActive' => $this->getIsActive(),
        ];
    }

    public function createTeamMember(AddTeamMemberInputPayload $data): self
    {
        $this->email = $data->email;
        $this->phone = $data->phone;
        $this->firstName = $data->firstName;
        $this->lastName = $data->lastName;

        return $this;
    }
}
