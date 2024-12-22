<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\User;
use App\Enum\DifficultyEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints as Assert;

class AdminProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du programme',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom du programme est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le nom du programme doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom du programme ne peut pas dépasser {{ limit }} caractères.',
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
            ->add('startDate', null, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de début est obligatoire.',
                    ]),
                ]
            ])
            ->add('endDate', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de fin est obligatoire.',
                    ]),


                ]
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'mapped' => false,
            ])
            ->add('difficulty', EnumType::class, [
                'class' => DifficultyEnum::class,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prix est obligatoire.',
                    ]),
                    new Assert\Range([
                        'min' => 0,
                        'minMessage' => 'Le prix ne peut pas être inférieur à {{ limit }}.',
                    ])
                ]
            ])
            ->add('coach', EntityType::class, [
                'class' => Coach::class,
                'choice_label' => 'firstName',
                'label' => 'Coach responsable',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le coach responsable est obligatoire.',
                    ])
                ]
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
                'multiple' => true,
                'label' => 'Utilisateurs associés',
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Catégories',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
