<?php

namespace App\Form;

use App\Entity\Publications;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenuPublication')
            ->add('urlFile', FileType::class, [
                'label' => 'Image (JPEG, PNG, GIF)',
                'mapped' => false, // This field is not mapped to any entity property
                'required' => true, // The file upload is optional
                'attr' => [
                    'class' => 'upload-button',
                    'accept' => 'image/*', // Accept only image files
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
