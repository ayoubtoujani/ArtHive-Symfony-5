<?php

namespace App\Form;

use App\Entity\Users;
use DateTime;
use App\Services\CountryApi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use App\Validator\Constraints\UniqueEmailEdit;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditUserFormType extends AbstractType
{
    private $countryApi;

    public function __construct(CountryApi $countryApi)
    {
        $this->countryApi = $countryApi;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomUser', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your name.']),
                new Regex([
                    'pattern' => '/^\D+$/',
                    'message' => 'Your name cannot contain numbers.',
                ]),
            ],
        ])

        ->add('prenomUser', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your surname.']),
                new Regex([
                    'pattern' => '/^\D+$/',
                    'message' => 'Your surname cannot contain numbers.',
                ]),
            ],
        ])

        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your email.']),
                new Email(['message' => 'The email "{{ value }}" is not a valid email.']),
                new UniqueEmailEdit([
                    'originalEmail' => $options['data']->getEmail(),
                ]),
            ],
        ])

        ->add('dNaissanceUser', DateType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new NotBlank(['message' => 'Please enter your date of birth.']),
                new Callback([$this, 'validateAge']),
            ],
        ])

        ->add('ville', ChoiceType::class, [
            'choices' => $this->getCountryChoices(),
            'constraints' => [
                new NotBlank(['message' => 'Please select your country.']),
            ],
        ])

        ->add('numTelUser', TelType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your phone number.']),
                new Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'Your phone number should contain only numbers.',
                ]),
            ],
        ]);
    }

    public function validateAge($value, ExecutionContextInterface $context)
    {
        $today = new \DateTime();
        $age = $today->diff($value)->y;
        if ($age < 18) {
            $context->buildViolation('You must be at least 18 years old.')->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }

    private function getCountryChoices(): array
    {
        $countries =  $this->countryApi->getCountries();

        $choices = ['Countries' => ''];
        foreach ($countries as $country) {
            $choices[$country] = $country;
        }
        asort($choices);
        return $choices;
    }
}
