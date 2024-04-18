<?php

namespace App\Form;

use App\Entity\Publications;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;



class AddPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
   
            
            ->add('contenuPublication', TextType::class, [
                'label' => 'Contenu de la publication',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un contenu pour la publication',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le contenu de la publication doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        
                    ]),
                  
                ],
            ])
            ->add('urlFile', FileType::class, [
                'label' => 'Image associée à l\'publication',
                'required' => true, // Rendre le champ non requis
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '5M', // Limite la taille du fichier à 1 Mo
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez fournir une image au format JPEG ou PNG',
                        'uploadErrorMessage' => 'Une erreur est survenue lors du téléchargement de l\'image',
                        'uploadFormSizeErrorMessage' => 'Le fichier est trop volumineux',
                        'disallowEmptyMessage' => 'Veuillez fournir une image',
                    ]),
                ],              

            ])
        ;
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publications::class,
        ]);
    }
}
