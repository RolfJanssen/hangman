<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 14-4-15
 * Time: 13:57
 */

namespace Hangman\Bundle\ApiBundle\Services\Exceptions;

use Exception;

/**
 * Class LetterNotValidException
 * @package Hangman\Bundle\ApiBundle\Services\Exceptions
 *
 * Exception thrown when letter does not pass regex validation
 */
class LetterNotValidException extends Exception {

}