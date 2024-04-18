<?php

namespace App\Form;

use App\Entity\Publications;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Component\Form\Extension\Core\Type\TextType;


class UpdatePostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('contenuPublication', TextareaType::class, [
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
        ]);



        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publications::class,
        ]);
    }
}
