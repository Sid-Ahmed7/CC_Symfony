<?php

namespace App\Form\Admin;

use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use App\Enum\SessionStatusEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

class AdminSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le titre est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('date', null, [
                'label' => 'Date de la session',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de la session est obligatoire.',
                    ]),
                ]
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La durée est obligatoire.',
                    ]),
                    new Assert\Positive([
                        'message' => 'La durée doit être un nombre positif.',
                    ])
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Lieu',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le lieu est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le lieu ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('status', EnumType::class, [
                'label' => 'Statut',
                'class' => SessionStatusEnum::class,
            ])
            ->add('program', EntityType::class, [
                'class' => Program::class,
                'choice_label' => 'name', 
                'label' => 'Programme',
                'choices' => $options['programs'], 
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le programme est obligatoire.',
                    ])
                ]
            ])
            ->add('coach', EntityType::class, [
                'class' => Coach::class,
                'choice_label' => function (Coach $coach) {
        return $coach->getFirstname() . ' ' . $coach->getLastname();
    },
                'label' => 'Coach',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le coach est obligatoire.',
                    ])
                ]
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
