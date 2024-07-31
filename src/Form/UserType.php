<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre ancien mot de passe',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'label' => 'Nouveau mot de passe',
                    'label_attr' => ['class' => 'form-label mt-4'],
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'label' => 'Confirmez le nouveau mot de passe',
                    'label_attr' => ['class' => 'form-label mt-4'],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&])/',
                        'message' => 'Votre mot de passe doit contenir au moins un chiffre, une lettre minuscule, une lettre majuscule et un caractère spécial',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
