<?php

namespace App\Form;

use App\Entity\ModuleSession;
use App\Entity\Programme;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbJours')
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'intitule',
            ])
            ->add('module', EntityType::class, [
                'class' => ModuleSession::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
