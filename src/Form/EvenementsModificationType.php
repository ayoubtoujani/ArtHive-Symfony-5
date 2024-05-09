<?php
namespace App\Form;

use App\Entity\Evenements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType; // Import DateType
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;

class EvenementsModificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titreEvenement', TextType::class, [
                'label' => 'Titre de l\'événement'
            ])
            ->add('dDebutEvenement', DateTimeType::class, [
                'label' => 'Date de début de l\'événement',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez fournir une date de début d\'événement.'
                    ]),
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(), // La date actuelle
                        'message' => 'La date de début ne peut pas être antérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('dFinEvenement', DateTimeType::class, [ 
                'label' => 'Date de fin de l\'événement',
                'widget' => 'single_text', 
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez fournir une date de début d\'événement.'
                    ]),
                    new GreaterThanOrEqual([
                        'propertyPath' => '[dDebutEvenement]', 
                        'message' => 'La date de fin doit être postérieure ou égale à la date de début.',
                    ]),
                ],
            ])
            ->add('descriptionEvenement', TextareaType::class, [
                'label' => 'Description de l\'événement'
            ])
            ->add('lieuEvenement', TextType::class, [
                'label' => 'Lieu de l\'événement'
            ])
            ->add('categorieevenement', ChoiceType::class, [
                'label' => 'Catégorie de l\'événement',
                'choices' => [
                    'PEINTURE' => 'PEINTURE',
                    'SCULPTURE' => 'SCULPTURE',
                    'MUSIQUE' => 'MUSIQUE',
                    'DANSE' => 'DANSE',
                    'CINEMA' => 'CINEMA',
                    'THEATRE' => 'THEATRE',
                    'PHOTOGRAPHIE' => 'PHOTOGRAPHIE',
                    'ART_NUMERIQUE' => 'ART_NUMERIQUE',
                    'ART_URBAIN' => 'ART_URBAIN',
                    'LITTERATURE' => 'LITTERATURE',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
            'image_required' => false, // Définition de l'option image_required avec une valeur par défaut
            'empty_data' => function ($form) {
                $startDate = $form->get('dDebutEvenement')->getData();
                $endDate = $form->get('dFinEvenement')->getData();
    
                // Vérifie si la date de début est null, sinon utilise la date actuelle
                $startDateTime = $startDate ? new \DateTime($startDate->format('Y-m-d H:i:s')) : new \DateTime();
    
                // Vérifie si la date de fin est null, sinon utilise la date actuelle
                $endDateTime = $endDate ? new \DateTime($endDate->format('Y-m-d H:i:s')) : new \DateTime();
    
                return new Evenements($startDateTime, $endDateTime);
            },
        ]);
    }
}
