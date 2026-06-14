<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Review;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('rating', IntegerType::class, [
            'label' => 'Értékelés (1-5)',
            'attr' => [
                'class' => 'form-control',
                'min' => 1,
                'max' => 5,
                'step' => 1,
            ],
        ])

        ->add('review_text', null, [
            'label' => 'Vélemény',
            'attr' => [
                'class' => 'form-control',
            ],
        ])

        ->add('author_email', EmailType::class, [
            'label' => 'Email cím',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('company', EntityType::class, [
            'class' => Company::class,
            'choice_label' => 'name',
            'placeholder' => 'Válassz céget',
            'label' => 'Cég',
            'attr' => [
                'class' => 'form-select',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            'csrf_protection' => true,
            'csrf_field_name' => 'custom_token_name',
            'csrf_token_id' => 'review_item',
        ]);
    }
}
