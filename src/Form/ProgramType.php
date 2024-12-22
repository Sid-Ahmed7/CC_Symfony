<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\User;
use App\Enum\DifficultyEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du programme',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du programme',
                'attr' => [ 'rows' => 5],
                'required' => true,
            ])

            ->add('startDate', null, [
                'label' => 'Début du programme',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('endDate', null, [
                'label' => 'Fin du programme',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Image du programme',
                'mapped' => false,
                'required' => true,
            ])
            ->add('difficulty', EnumType::class, [
                'class' => DifficultyEnum::class,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => 'EUR',
                'required' => true,
            ])
            ->add('coach', EntityType::class, [
                'class' => Coach::class,
                'choice_label' => function (Coach $coach) {
                 return $coach->getFirstname() . ' ' . $coach->getLastname();
                }
                ,
                'label' => 'Nom du coach',
                'required' => true,
            ])

            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, 
                'label' => 'Categories',
                'required' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
