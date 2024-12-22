<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Speciality;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterCoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'Prénom',
            'required' => true,
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Nom',
            'required' => true,
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
            'mapped' => false,
            'required' => true,
        ])

        ->add('specialities', EntityType::class, [
            'class' => Speciality::class,
            'choice_label' => 'name', 
            'multiple' => true, 
            'expanded' => true,  
            'label' => 'Spécialités',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
