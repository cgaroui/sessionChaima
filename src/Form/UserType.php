<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'required' => true,
        ])
        ->add('pseudo', TextType::class, [
            'required' => true,
        ])
        ->add('oldPassword', PasswordType::class, [
            'mapped' => false,
            'required' => true,
            'label' => 'Ancien mot de passe',
        ])
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'required' => true,
            'label' => 'Nouveau mot de passe',
        ]);
}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => User::class,
    ]);
}
}
