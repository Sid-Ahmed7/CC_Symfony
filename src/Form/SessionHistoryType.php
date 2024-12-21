<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\SessionHistory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionHistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sessionDate', null, [
                'widget' => 'single_text',
            ])
            ->add('goals', TextareaType::class)
            ->add('comments', TextareaType::class)
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'title',
                'choices' => $options['user_sessions_query']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionHistory::class,
            'user_sessions_query' => [],
        ]);
    }
}
