<?php

namespace App\Form;

use App\Entity\Commandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CommandesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomClient', TextType::class, [
            'label' => 'Nom',
        ])
        ->add('prenomClient', TextType::class, [
            'label' => 'Prénom',
        ])
        ->add('telephone', TextType::class, [
            'label' => 'Téléphone',
        ])
        ->add('eMail', EmailType::class, [
            'label' => 'Email',
        ])
        ->add('adresseLivraison', TextType::class, [
            'label' => 'Adresse de livraison',
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commandes::class,
        ]);
    }
}
