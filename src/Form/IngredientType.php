<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'ingrédient',
                'attr' => [
                    'placeholder' => 'Exemple : 200g de pommes de terre',
                    'class' => 'form-control',
                ],
            ]);
            // ->add('quantite', NumberType::class, [
            //     'label' => 'Quantité',
            //     'required' => false,
            //     'attr' => [
            //         'placeholder' => 'Exemple : 200',
            //         'class' => 'form-control',
            //     ],
            // ])
            // ->add('unite', TextType::class, [
            //     'label' => 'Unité',
            //     'required' => false,
            //     'attr' => [
            //         'placeholder' => 'Exemple : g, ml, càs',
            //         'class' => 'form-control',
            //     ],
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
