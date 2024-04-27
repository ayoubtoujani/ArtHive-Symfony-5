<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEmailEdit extends Constraint
{
    public $message = 'This email is already in use.';
    public $originalEmail = null; // Add this line

    public function getRequiredOptions()
    {
        return ['originalEmail']; // Add this line
    }
}