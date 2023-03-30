<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RefExamenRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RefExamenRepository::class)]
class RefExamen
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'intitulé de l'examen est requis.")]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de questions est requis.")]
    #[Assert\Positive(message: "Le nombre de questions doit être supérieur à zero.")]
    private ?int $numberOfQuestions = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de minutes est requis.")]
    #[Assert\Positive(message: "Le nombre de minutes doit être supérieur à zero.")]
    private ?int $numberOfMinutesToAnswer = null;

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

    public function getNumberOfQuestions(): ?int
    {
        return $this->numberOfQuestions;
    }

    public function setNumberOfQuestions(int $numberOfQuestions): self
    {
        $this->numberOfQuestions = $numberOfQuestions;

        return $this;
    }

    public function getNumberOfMinutesToAnswer(): ?int
    {
        return $this->numberOfMinutesToAnswer;
    }

    public function setNumberOfMinutesToAnswer(int $numberOfMinutesToAnswer): self
    {
        $this->numberOfMinutesToAnswer = $numberOfMinutesToAnswer;

        return $this;
    }
}
