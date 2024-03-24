<?php

namespace App\Form;

use App\Entity\Users;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterFormType extends AbstractType
{
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
            ],
        ])
        ->add('mdpUser', PasswordType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your password.']),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters.',
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
        ->add('ville')
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
}
