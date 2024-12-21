<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use App\Enum\SessionStatusEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la séance',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la séance',
                'required' => true,
            ])
            ->add('date', null, [
                'label' => 'Date de la séance',
                'widget' => 'single_text',
               'required' => true,
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée de la séance',
                'required' => true,
            ])
            ->add('location', TextType::class, [
                'label' => 'Lieu de la séance',
                'required' => true,
            ])
            ->add('status', EnumType::class, [
                'class' => SessionStatusEnum::class,
                'choice_label' => function(SessionStatusEnum $status) {
        return $status->value;  
    },
            ])
            ->add('program', EntityType::class, [
                'class' => Program::class,
                'choice_label' => 'name',
                'choices' => $options['programs'],
                'required' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
            'programs' => [],
        ]);
    }
}
