<?php
// src/Form/EvenementsType.php
namespace App\Form;

use App\Entity\Evenements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType; // Importez DateType
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\DataTransformer\ImageToFileTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;



class EvenementsType extends AbstractType
{
    private $transformer;

    public function __construct(ImageToFileTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('titreEvenement', TextType::class, [
                'label' => 'Titre de l\'événement'
            ])
            ->add('dDebutEvenement', DateTimeType::class, [
                'label' => 'Date de début de l\'événement',
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(),
                'constraints' => [
                   
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(), // La date actuelle
                        'message' => 'La date de début ne peut pas être antérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('dFinEvenement', DateTimeType::class, [ // Utilisez DateType au lieu de DateTimeType
                'label' => 'Date de fin de l\'événement',
                'widget' => 'single_text', // Utilisez 'single_text' pour afficher le champ en tant que champ texte simple
                
            ])

            
            ->add('descriptionEvenement', TextareaType::class, [
                'label' => 'Description de l\'événement'
            ])
            ->add('lieuEvenement', TextType::class, [
                'label' => 'Lieu de l\'événement'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image associée à l\'événement',
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
                'required' => true, // Définir à true si la sélection est obligatoire
                // Vous pouvez également spécifier d'autres options comme 'expanded' => true pour afficher les choix sous forme de boutons radio
            ]);
          
           
            $builder->get('image')->addModelTransformer($this->transformer);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
            'image_required' => true, // Option pour indiquer si le champ image est requis ou non

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


