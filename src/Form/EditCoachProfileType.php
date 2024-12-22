<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Speciality;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCoachProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['placeholder' => 'Votre prénom']
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Nom',
            'attr' => ['placeholder' => 'Votre nom']
        ])
        
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['placeholder' => 'Votre email']
        ])
        ->add('phone', TelType::class, [
            'label' => 'Numéro de téléphone',
            'required' => true,
        ])
        ->add('bio', TextareaType::class, [
            'label' => 'Biographie',
            'required' => true,  
            'attr' => ['rows' => 5],  
        ])
            ->add('profilePicture', FileType::class, [
                'required' => false,
                'mapped' => false, 
                'label' => 'Photo de profil',
            ])
            ->add('specialities', EntityType::class, [
                'class' => Speciality::class,
                'choice_label' => 'id',
                'multiple' => true,
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
