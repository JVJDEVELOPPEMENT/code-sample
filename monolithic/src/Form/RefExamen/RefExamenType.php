<?php

declare(strict_types=1);

namespace App\Form\RefExamen;

use App\Entity\RefExamen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'label' => 'Intitulé',
                'required' => false
            ])
            ->add('numberOfQuestions',IntegerType::class,[
                'label' => 'Nombre de questions',
                'required' => false
            ])
            ->add('numberOfMinutesToAnswer',IntegerType::class,[
                'label' => 'Nombre de minutes pour répondre',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RefExamen::class,
        ]);
    }
}