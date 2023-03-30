<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Candidature\NumberOfQuestionsAskableNumber;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le type d'examen est requis.")]
    #[NumberOfQuestionsAskableNumber]
    private ?RefExamen $refExamen = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result = null;

    #[ORM\OneToOne(mappedBy: 'candidature', cascade: ['persist', 'remove'])]
    private ?Quiz $quiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefExamen(): ?RefExamen
    {
        return $this->refExamen;
    }

    public function setRefExamen(?RefExamen $refExamen): self
    {
        $this->refExamen = $refExamen;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): self
    {
        // set the owning side of the relation if necessary
        if ($quiz->getCandidature() !== $this) {
            $quiz->setCandidature($this);
        }

        $this->quiz = $quiz;

        return $this;
    }
}