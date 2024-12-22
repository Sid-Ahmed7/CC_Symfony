<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserProfileType extends AbstractType
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
            
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                'attr' => ['placeholder' => 'jj/mm/aaaa']
            ])
            
            ->add('weight', NumberType::class, [
                'label' => 'Poids',
                'attr' => ['placeholder' => 'Votre poids']
            ])
            ->add('height', NumberType::class, [
                'label' => 'Taille',
                'attr' => ['placeholder' => 'Votre taille']
            ])
            
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
