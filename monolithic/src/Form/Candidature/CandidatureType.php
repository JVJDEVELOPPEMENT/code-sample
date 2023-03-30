<?php

declare(strict_types=1);

namespace App\Form\Candidature;

use App\Entity\Candidature;
use App\Entity\RefExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('refExamen',EntityType::class,[
                'label' => 'Sur quel type d\'examen se baser ?',
                'class' => RefExamen::class,
                'required' => false,
                'choice_label' => 'title',
                'placeholder' => '--Sélectionner--'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}