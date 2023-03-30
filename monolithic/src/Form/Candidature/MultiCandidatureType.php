<?php

declare(strict_types=1);

namespace App\Form\Candidature;

use App\Entity\Candidate;
use App\Entity\RefExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class MultiCandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refExamen',EntityType::class,[
                'label' => 'Sur quel type d\'examen se baser ?',
                'class' => RefExamen::class,
                'required' => false,
                'choice_label' => 'title',
                'placeholder' => '--Sélectionner--',
                'constraints' => [
                    new NotNull([
                        'message' => 'Vous devez sélectionner un type d\'examen'
                    ])
                ]
            ])

            ->add('candidate',EntityType::class,[
                'multiple' => true,
                'attr' => [
                    'class' => 'select2 form-control select2-multiple select-option',
                    'data-toggle' => 'select2'
                ],
                'label' => 'Sélectionner les candidats',
                'class' => Candidate::class,
                'required' => false,
                'choice_label' => 'displayName',
                'placeholder' => '--Sélectionner--',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}