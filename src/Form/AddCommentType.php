<?php

namespace App\Form;

use App\Entity\Commentaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;


use App\Services\BadWordsService;

class AddCommentType extends AbstractType
{
    private $badWordsService;

    public function __construct(BadWordsService $badWordsService)
    {
        $this->badWordsService = $badWordsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenuCommentaire', TextareaType::class, [
                'label' => 'Write a Comment Here:',
                'constraints' => [
                    new NotBlank([
                        'message' => 'The comment cannot be empty.',
                    ]),
                    new Callback([$this, 'validateContent']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }

    public function validateContent($value, ExecutionContextInterface $context)
    {
        if ($this->badWordsService->containsBadWord($value)) {
            $context->buildViolation('Your comment contains inappropriate language.')->addViolation();
        }
    }
}
