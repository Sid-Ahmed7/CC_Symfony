<?php

namespace App\Form\Admin;
use App\Entity\User;
use App\Entity\Coach;
use App\Entity\Speciality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class AdminCoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prénom est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire.',
                    ]),
                    new Assert\Email([
                        'message' => 'L\'email "{{ value }}" n\'est pas valide.',
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le mot de passe est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 255,
                    ])
                    ],
                    'required' => !$options['is_edit'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le numéro de téléphone est obligatoire.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\+?\d{1,4}?\s?\(?\d+\)?[\d\s\-]+$/',
                        'message' => 'Le numéro de téléphone n\'est pas valide.',
                    ])
                ]
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La biographie est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'La biographie ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil',
                'required' => true,
                'mapped' => false

                
            ])
            ->add('rating', NumberType::class, [
                'label' => 'Note ',
                'constraints' => [
                    new Assert\Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}.',
                    ])
                ],
                'required' => false
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
                'multiple' => true,
                'label' => 'Utilisateurs associés',
                'required' => false,
            ])
            ->add('specialities', EntityType::class, [
                'class' => Speciality::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Spécialités',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
            'is_edit' => false,
        ]);
    }
}
