<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 3-11-14
 * Time: 20:00
 */

namespace Hangman\Bundle\ApiBundle\Services\Exceptions;

use Exception;

/**
 * Class InvalidGameStatusException
 * @package Hangman\Bundle\ApiBundle\Services\Exceptions
 *
 * Exception thrown when game is already ended but server request are still coming in.
 */
class InvalidGameStatusException extends Exception {

}