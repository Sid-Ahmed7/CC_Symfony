<?php

namespace App\Form\Admin;

use App\Entity\Coach;
use App\Entity\Speciality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class AdminSpecialityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la spécialité',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de la spécialité est obligatoire.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de la spécialité ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('coaches', EntityType::class, [
                'class' => Coach::class,
                'choice_label' => 'id',
                'multiple' => true,
                'label' => 'Coachs associés',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner au moins un coach.',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Speciality::class,
        ]);
    }
}
