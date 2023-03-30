<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    const PENDING = "En attente";
    const STARTED = "Démarré";
    const FINISHED = "Terminé";
    const CANCELED = "Annulé";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numberOfQuestions = null;

    #[ORM\Column]
    private ?int $numberOfMinutesToAnswer = null;

    #[ORM\Column(length: 255)]
    private ?string $status = self::PENDING;

    #[ORM\Column(length: 255)]
    private ?string $tokenLink = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $score = null;

    #[ORM\OneToOne(inversedBy: 'quiz', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidature $candidature = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: QuizQuestion::class, orphanRemoval: true)]
    private Collection $quizQuestions;

    public function __construct()
    {
        $this->quizQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTokenLink(): ?string
    {
        return $this->tokenLink;
    }

    public function setTokenLink(string $tokenLink): self
    {
        $this->tokenLink = $tokenLink;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCandidature(): ?Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(Candidature $candidature): self
    {
        $this->candidature = $candidature;

        return $this;
    }

    /**
     * @return Collection<int, QuizQuestion>
     */
    public function getQuizQuestions(): Collection
    {
        return $this->quizQuestions;
    }

    public function addQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if (!$this->quizQuestions->contains($quizQuestion)) {
            $this->quizQuestions->add($quizQuestion);
            $quizQuestion->setQuiz($this);
        }

        return $this;
    }

    public function removeQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if ($this->quizQuestions->removeElement($quizQuestion)) {
            // set the owning side to null (unless already changed)
            if ($quizQuestion->getQuiz() === $this) {
                $quizQuestion->setQuiz(null);
            }
        }

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        $candidature = $this->getCandidature();
        if(null !== $candidature)
        {
            return $candidature->getCandidate();
        }

        return null;
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

    public function getNumberOfCorrectAnswers(): int
    {
        $total = 0;

        $quizQuestions = $this->getQuizQuestions();

        /** @var QuizQuestion $quizQuestion */
        foreach ($quizQuestions as $quizQuestion)
        {
            if($quizQuestion->getIsAnsweredCorrectly() === true)
            {
                $total++;
            }
        }

        return $total;
    }

    public function getNumberOfBadAnswers(): int
    {
        $total = 0;

        $quizQuestions = $this->getQuizQuestions();

        /** @var QuizQuestion $quizQuestion */
        foreach ($quizQuestions as $quizQuestion)
        {
            if($quizQuestion->getIsAnsweredCorrectly() !== true)
            {
                $total++;
            }
        }

        return $total;
    }
}