<?php

namespace App\Validator\Constraints;

use App\Entity\Users;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class UniqueEmailValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {

        $existingUser = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $value]);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}