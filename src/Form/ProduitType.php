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
class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('imageProduit', FileType::class, [
            'label' => 'Image',
            'mapped' => false, // This field is not mapped to any entity property
            'required' => false, // The file upload is optional
            'attr' => [
                'class' => 'upload-button',
                'accept' => 'image/*', // Accept only image files
            ],
        ])

        ->add('nomProduit', TextType::class, [
            'label' => 'Nom du produit'
        ])

      
        ->add('prixProduit', IntegerType::class, [
            'label' => 'Prix du produit',
            'attr' => [
                'min' => 0,
                'max' => 100000, 
            ]
        ])
        ->add('descriptionProduit', TextareaType::class, [
            'label' => 'Description du produit'
        ])
        ->add('disponibilite', CheckboxType::class, [
            'label' => 'Disponibilité du produit',
            'required' => false,
        ])
        ->add('stockProduit', IntegerType::class, [
            'label' => 'Stock du produit',
            'attr' => [
                'min' => 0,
                'max' => 1000, 
            ]
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
