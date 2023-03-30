<?php

namespace App\Form\Company;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                'label' => 'Intitulé',
                'required' => false
            ])
            ->add('website',TextType::class,[
                'label' => 'Site Web',
                'required' => false
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville',
                'required' => false
            ])
            ->add('street',TextType::class,[
                'label' => 'Rue',
                'required' => false
            ])
            ->add('country',TextType::class,[
                'label' => 'Pays',
                'required' => false
            ])
            ->add('employeeSize',ChoiceType::class,[
                'label' => 'Nombre d\'employés',
                'placeholder' => '--Sélectionner--',
                'choices' => [
                    "1-5" => "1-5",
                    "6-25" => "6-25",
                    "26-50" => "26-50",
                    "51-100" => "51-100",
                    "101-250" => "101-250",
                    "250+" => "250+",
                ],
                'required' => false
            ])
            ->add('sector',TextType::class,[
                'label' => 'Secteur',
                'required' => false
            ])
            /*
                ->add('foundedAt',DateType::class,[
                    'placeholder' => false,
                    'label' => 'Date de fondation',
                    'format' => 'MM/dd/yyyy',
                    'widget' => 'single_text',
                    'attr' => ['class' => 'form-control date', 'data-toggle' => 'date-picker', 'data-single-date-picker' => 'true'],
                    'html5' => false,
                    'required' => false,
                    'mapped' => false
                ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
