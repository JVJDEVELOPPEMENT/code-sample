<?php

namespace App\Entity;

use App\Repository\QuizQuestionRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: QuizQuestionRepository::class)]
class QuizQuestion
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quizQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $inputType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?RefQuestion $refQuestion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAnsweredCorrectly = null;

    #[ORM\OneToMany(mappedBy: 'quizQuestion', targetEntity: QuizAnswer::class, orphanRemoval: true)]
    private Collection $quizAnswers;

    public function __construct()
    {
        $this->quizAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getRefQuestion(): ?RefQuestion
    {
        return $this->refQuestion;
    }

    public function setRefQuestion(?RefQuestion $refQuestion): self
    {
        $this->refQuestion = $refQuestion;

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

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getIsAnsweredCorrectly(): ?bool
    {
        return $this->isAnsweredCorrectly;
    }

    public function setIsAnsweredCorrectly(?bool $isAnsweredCorrectly): self
    {
        $this->isAnsweredCorrectly = $isAnsweredCorrectly;

        return $this;
    }

    /**
     * @return Collection<int, QuizAnswer>
     */
    public function getQuizAnswers(): Collection
    {
        return $this->quizAnswers;
    }

    public function addQuizAnswer(QuizAnswer $quizAnswer): self
    {
        if (!$this->quizAnswers->contains($quizAnswer)) {
            $this->quizAnswers->add($quizAnswer);
            $quizAnswer->setQuizQuestion($this);
        }

        return $this;
    }

    public function removeQuizAnswer(QuizAnswer $quizAnswer): self
    {
        if ($this->quizAnswers->removeElement($quizAnswer)) {
            // set the owning side to null (unless already changed)
            if ($quizAnswer->getQuizQuestion() === $this) {
                $quizAnswer->setQuizQuestion(null);
            }
        }

        return $this;
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

    public function getInputType(): ?string
    {
        return $this->inputType;
    }

    public function setInputType(string $inputType): self
    {
        $this->inputType = $inputType;

        return $this;
    }

    public function getCorrectAnswerIds(): array
    {
        $ids = [];

        $answers = $this->getQuizAnswers();

        /** @var QuizAnswer $answer */
        foreach ($answers as $answer)
        {
            if(true === $answer->getIsCorrect())
            {
                $ids[] = $answer->getId();
            }
        }

        return $ids;
    }
}