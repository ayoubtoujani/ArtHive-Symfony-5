<?php

namespace App\Form;

use App\Entity\Commandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;


class CommandesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomClient', TextType::class, [
            'label' => 'Nom',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                ]),
            ],       
        ])
        ->add('prenomClient', TextType::class, [
            'label' => 'Prénom',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                ]),
            ],     
        ])

        ->add('telephone', NumberType::class, [ 
            'label' => 'Téléphone',
            'constraints' => [
                new NotBlank([
                    'message' => 'Le téléphone ne peut pas être vide.',
                ]),
                new Length([
                    'min' => 8,
                    'max' => 8,
                    'exactMessage' => 'Le téléphone doit contenir exactement {{ limit }} chiffres.',
                ]),
                new Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'Le téléphone ne peut contenir que des chiffres.',
                ]),
            ],
        ])
        
        ->add('eMail', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'email ne peut pas être vide.',
                ]),
                new Email([
                    'message' => 'L\'email {{ value }} n\'est pas valide.',
                ]),
            ],
        ])
        ->add('adresseLivraison', TextType::class, [
            'label' => 'Adresse de livraison',
            'constraints' => [
                new NotBlank([
                    'message' => 'L\'adresse de livraison ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'L\'adresse de livraison doit contenir au moins {{ limit }} caractères.',
                ]),
            ],  
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commandes::class,
        ]);
    }
}
