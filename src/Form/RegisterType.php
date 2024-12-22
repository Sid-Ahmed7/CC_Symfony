<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'Prénom',
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Nom',
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

        ->add('birthDate', DateType::class, [
            'widget' => 'single_text',
            'html5' => false, 
            'label' => 'Date de naissance',
            'input' => 'datetime', 
            'format' => 'dd-MM-yyyy', 
        ])
        ->add('weight', NumberType::class, [
            'label' => 'Poids (en kg)',
            'attr' => ['step' => '0.1', 'min' => '0'],
        ])
        ->add('height', NumberType::class, [
            'label' => 'Taille (en cm)',
            'attr' => ['step' => '0.1', 'min' => '0'],
        ])
        ->add('profilePicture', FileType::class, [
            'label' => 'Photo de profil',
            'mapped' => false,
            'required' => true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
