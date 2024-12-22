<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Speciality;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'label' => 'Adresse email',
            'required' => true,
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'required' => true,
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
