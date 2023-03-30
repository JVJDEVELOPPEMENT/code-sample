<?php

declare(strict_types=1);

namespace App\Form\RefQuestion;

use App\Entity\RefQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'label' => 'Intitulé',
                'required' => false
            ])
            ->add('inputType',ChoiceType::class,[
                'label' => 'Type de question',
                'placeholder' => '--Sélectionner--',
                'choices' => [
                    RefQuestion::QUESTION_MULTIPLE_CHOICES => RefQuestion::QUESTION_MULTIPLE_CHOICES,
                    RefQuestion::QUESTION_UNIQUE_CHOICE => RefQuestion::QUESTION_UNIQUE_CHOICE
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RefQuestion::class,
        ]);
    }
}