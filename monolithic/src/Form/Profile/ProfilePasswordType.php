<?php

declare(strict_types=1);

namespace App\Form\Profile;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => ['class' => 'form-control form-control-sm'],
                'required' => false,
                'label' => 'Votre mot de passe actuel',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre ancien mot de passe',
                    ]),
                    new UserPassword([
                        'message' => 'Votre mot de passe actuel n\'est pas correct.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'attr' => ['class' => 'form-control form-control-sm'],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Vous devez renseigner un mot de passe',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du nouveau mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Vous devez renseigner une confirmation de mot de passe',
                        ]),
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}