<?php

declare(strict_types=1);

namespace App\Form\Quiz;

use App\Entity\QuizAnswer;
use App\Entity\QuizQuestion;
use App\Entity\RefQuestion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class QuizQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuizQuestion $quizQuestion */
        $quizQuestion = $options['data'];

        $inputType = $quizQuestion->getInputType();

        if($inputType === RefQuestion::QUESTION_UNIQUE_CHOICE)
        {
            $constraintsMessage = "Vous devez sélectionner une réponse.";
        }
        else
        {
            $constraintsMessage = "Vous devez sélectionner une ou plusieurs réponse(s).";
        }

        $choices = [];

        /** @var QuizAnswer $answer */
        foreach ($quizQuestion->getQuizAnswers() as $answer)
        {
            $choices[$answer->getTitle()] = $answer->getId();
        }

        $builder
            ->add('answers',ChoiceType::class,[
                'choices' => $choices,
                'multiple' => $inputType === RefQuestion::QUESTION_MULTIPLE_CHOICES,
                'expanded' => true,
                'required' => false,
                'mapped' => false,
                'label' => false,
                'placeholder' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $constraintsMessage
                    ])
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizQuestion::class
        ]);
    }
}