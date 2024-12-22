<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'label' => 'Adresse email',
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
