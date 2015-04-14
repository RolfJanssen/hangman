<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 31-10-14
 * Time: 17:43
 */
namespace Hangman\Bundle\ApiBundle\Services;

use \Doctrine\ORM\EntityManager;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Hangman\Bundle\ApiBundle\Services\Exceptions\InvalidGameStatusException;
use Hangman\Bundle\ApiBundle\Services\Exceptions\LetterNotValidException;

/**
 * Class HangmanGameService
 * @package Hangman\Bundle\ApiBundle\Services
 */
class HangmanGameService {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Check if given letter is a valid letter
     * if letter is part of the word, update the addCharactersGuessed parameter of the current game object
     * if letter is not part of the word, decrease the triesLeft parameter of the game
     * Finish the game when word is guessed or when there are no tries left, by setting a game status of fail or success
     *
     * @param $gameId - id of current running game
     * @param $letter - letter which will be checked if it exists in the current game word
     * @return $currentGame
     * @throws InvalidGameStatusException
     */
    public function updateGame($currentGame, $letter)
    {
        if (!preg_match('/^[a-z]+$/', $letter, $matches))
        {
            throw new LetterNotValidException("Input does not have correct format", 400);
        }

        if ($currentGame->getStatus() == Game::STATUS_FAIL || $currentGame->getStatus() == Game::STATUS_SUCCESS)
        {
            throw new InvalidGameStatusException("It is impossible to continue a game which is already ended", 400);
        }

        $word = $currentGame->getWord();
        $currentWordCharacters = str_split($word);
        if (strpos($word, $letter) !== FALSE)
        {
            $currentGame->addCharacterGuessed($letter);
            $charactersGuessed = $currentGame->getCharactersGuessed();

            $arrayDiff = array_diff($currentWordCharacters, $charactersGuessed);
            if (count($arrayDiff) == 0)
            {
                $currentGame->setStatus(Game::STATUS_SUCCESS);
            }
        }
        else
        {
            $triesLeft = $currentGame->getTriesLeft();
            $triesLeft = $triesLeft - 1;
            $currentGame->setTriesLeft($triesLeft);

            if ($triesLeft == 0)
            {
                $currentGame->setStatus(Game::STATUS_FAIL);
            }
        }

        //TODO: check if entity based validation works
        $this->entityManager->persist($currentGame);
        $this->entityManager->flush();

        return $currentGame;
    }
}