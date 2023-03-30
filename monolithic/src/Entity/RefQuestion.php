<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RefQuestionRepository;
use App\Traits\ChangeTrackerTrait;
use App\Traits\SoftDeletableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: RefQuestionRepository::class)]
class RefQuestion
{
    use ChangeTrackerTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    const QUESTION_MULTIPLE_CHOICES = "Question à choix multiple (QCM)";
    const QUESTION_UNIQUE_CHOICE = "Question à choix simple (QCS)";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "L'intitulé de la question est requis.")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le choix du nombre de réponse possible est requis.")]
    private ?string $inputType = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: RefAnswer::class, orphanRemoval: true)]
    private Collection $refAnswers;

    public function __construct()
    {
        $this->refAnswers = new ArrayCollection();
    }

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

    public function getInputType(): ?string
    {
        return $this->inputType;
    }

    public function setInputType(string $inputType): self
    {
        $this->inputType = $inputType;

        return $this;
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

    /**
     * @return Collection<int, RefAnswer>
     */
    public function getRefAnswers(): Collection
    {
        return $this->refAnswers;
    }

    public function addRefAnswer(RefAnswer $refAnswer): self
    {
        if (!$this->refAnswers->contains($refAnswer)) {
            $this->refAnswers->add($refAnswer);
            $refAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeRefAnswer(RefAnswer $refAnswer): self
    {
        if ($this->refAnswers->removeElement($refAnswer)) {
            // set the owning side to null (unless already changed)
            if ($refAnswer->getQuestion() === $this) {
                $refAnswer->setQuestion(null);
            }
        }

        return $this;
    }

    public function isAskable(): bool
    {
        $total = 0;

        $refAnswers = $this->getRefAnswers();

        /** @var RefAnswer $refAnswer */
        foreach ($refAnswers as $refAnswer)
        {
            if(true === $refAnswer->getIsCorrect())
            {
                $total++;
            }
        }

        if($total > 0)
        {
            return true;
        }

        return false;
    }

    public function numberOfCorrectAnswer(): int
    {
        $total = 0;

        $refAnswers = $this->getRefAnswers();

        /** @var RefAnswer $refAnswer */
        foreach ($refAnswers as $refAnswer)
        {
            if(true === $refAnswer->getIsCorrect())
            {
                $total++;
            }
        }

        return $total;
    }
}
