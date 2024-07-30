<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        //cette partie fait reference à l'ancien mot de passe de l'utilisateur qui doit le saisir avant d'éditer (avant de saisir le nouveau)
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'current-password', 'class' => 'form-control'],
                'label' => 'Ancien mot de passe',
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre ancien mot de passe',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // au lieu d'être directement défini sur l'objet,
                // ceci est lu et encodé dans le contrôleur
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
                        // longueur maximale autorisée par Symfony pour des raisons de sécurité
                        'max' => 4096,
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
