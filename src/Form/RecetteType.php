<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Recette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre',
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de la recette',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Image de la recette (JPEG ou PNG, max 2 Mo)',
                    'class' => 'form-control mb-3'
                ],
                // Ajoutez les contraintes de validation pour l'image
                'constraints' => [
                    new ConstraintsFile([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, GIF).',
                    ]),
                ],
            ])
            ->add('tempsPreparation', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Temps de préparation (en minutes)',
                    'class' => 'form-control mb-3',
                ],
            ])
            ->add('nombrePersonne', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nombre de personnes',
                    'class' => 'form-control mb-3',
                ],
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            //     'label' => 'Crée le',
            //     'attr' => [
            //         'class' => 'form-control mb-3'
            //     ]
            // ])
            ->add('etapes', CollectionType::class, [
                'entry_type' => EtapeType::class, // Formulaire individuel pour chaque étape
                'allow_add' => true,             // Permet d'ajouter des étapes dynamiquement
                'allow_delete' => true,          // Permet de supprimer des étapes
                'by_reference' => false,         // Nécessaire pour une relation OneToMany
                'prototype' => true,
                'prototype_name' => '__name__',
                'label' => false,
                'attr' => [
                    'class' => 'etape-collection',
                ],
            ])

            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom', // Le champ de Category à afficher
                'label' => false,
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => [
                    'class' => 'form-select mb-3', // Ajoutez une classe Bootstrap si nécessaire
                ],
            ])

            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientType::class, // Utilise le formulaire IngredientType pour chaque ingrédient
                'allow_add' => true,                  // Permet d'ajouter dynamiquement des ingrédients
                'allow_delete' => true,               // Permet de supprimer des ingrédients
                'by_reference' => false,              // Nécessaire pour une relation OneToMany
                'label' => false,
                'attr' => [
                    'class' => 'ingredient-collection',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'csrf_protection' => true, // CSRF activé par défaut
            'csrf_field_name' => '_token', // Nom du champ CSRF
            'csrf_token_id'   => 'article_item', // ID unique pour ce formulaire
        ]);
    }
}
