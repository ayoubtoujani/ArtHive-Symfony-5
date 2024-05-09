<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotNull;


class ModifierProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomProduit', TextType::class, [
            'label' => 'Nom du produit',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom du produit ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom du produit doit contenir au moins {{ limit }} caractères.',
                ]),
            ],    
        ])

      
        ->add('prixProduit', NumberType::class, [
            'label' => 'Prix du produit',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prix du produit ne peut pas être vide.',
                ]),
            ],
            'attr' => [
                'min' => 0,
                'max' => 100000, 
                'step' => 0.1,
                
            ],
            'html5' => true,
        ])
        ->add('descriptionProduit', TextareaType::class, [
            'label' => 'Description du produit',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'La description du produit ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                ]),
            ],    
        ])
        ->add('disponibilite', CheckboxType::class, [
            'label' => 'Disponibilité du produit',
            'required' => false,
            'attr' => [
                'id' => 'disponibilite-checkbox', // Ajout de l'ID
            ]
           
        ])
        ->add('stockProduit', IntegerType::class, [
            'label' => 'Stock du produit',
            'attr' => [
                'min' => 0,
                'max' => 100000, 
                'id' => 'stock-field',
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prix du produit ne peut pas être vide.',
                ]),
            ],
        ])
        ->add('categProduit', ChoiceType::class, [
            'label' => 'Catégorie du produit',
            'choices' => [
                'Choisir une catégorie' => null,
                'Peinture' => 'PEINTURE',
                'Art IA' => 'AI_ART',
                'Pixel Art' => 'PIXEL_ART',
                'Sculpture' => 'SCULPTURE',
                'Photographie' => 'PHOTOGRAPHIE',
                'Anime' => 'ANIME',
                'Dessin' => 'DESSIN',
                'Art numérique' => 'DIGITAL_ART',
                'Illustration' => 'ILLUSTRATION',
                'Autre' => 'AUTRE',
            ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie.',
                    ]),
                ],
        ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
