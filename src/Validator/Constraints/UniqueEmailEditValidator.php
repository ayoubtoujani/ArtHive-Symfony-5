<?php

namespace App\Validator\Constraints;

use App\Entity\Users;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class UniqueEmailEditValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($constraint->originalEmail === $value) {
            return;
        }

        $existingUser = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $value]);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}