<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class AdminCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de la catégorie est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('programs', EntityType::class, [
                'class' => Program::class,
                'choice_label' => 'id',
                'multiple' => true,
                'label' => 'Programmes associés',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner au moins un programme.',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
