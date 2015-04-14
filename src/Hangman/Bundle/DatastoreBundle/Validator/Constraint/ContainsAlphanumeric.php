<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 2-11-14
 * Time: 17:22
 */

namespace Hangman\Bundle\DatastoreBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsAlphanumeric extends Constraint
{
    public $message = 'The string "%string%" contains an illegal character: it can only contain lowercase letters';

    public function validatedBy() {
        return get_class($this).'Validator';
    }
}