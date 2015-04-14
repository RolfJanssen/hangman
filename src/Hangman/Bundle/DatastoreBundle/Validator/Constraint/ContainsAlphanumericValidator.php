<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 2-11-14
 * Time: 17:23
 */

namespace Hangman\Bundle\DatastoreBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function __construct(ExecutionContextInterface $context) {
        $this->context = $context;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[a-z]+$/', $value, $matches)) {

            // If you're using the new 2.5 validation API (you probably are!)
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}