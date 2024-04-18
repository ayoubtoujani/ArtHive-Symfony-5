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



class UpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenuCommentaire', TextareaType::class, [
                'label' => 'Write a Comment Here:',
                'constraints' => [
                    new NotBlank([
                        'message' => 'The comment cannot be empty.',
                    ]),
                    new Assert\Callback([
                        'callback' => function ($comment, ExecutionContextInterface $context) {
                            $badWords = ['bad', 'words', 'list'];
                            foreach ($badWords as $word) {
                                if (stripos($comment, $word) !== false) {
                                    $context->buildViolation('The comment contains inappropriate language.')
                                        ->addViolation();
                                    break;
                                }
                            }
                        },
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}
