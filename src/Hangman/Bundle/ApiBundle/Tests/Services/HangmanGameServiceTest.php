<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 14-4-15
 * Time: 15:51
 */

namespace Hangman\Bundle\HangmanBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;
use Hangman\Bundle\ApiBundle\Services\HangmanGameService;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

class HangmanGameServiceTest extends PHPUnit_Framework_TestCase
{
    const RANDOM_WORD = 'randomword';
    const ONE_TRY_LEFT = 1;
    const NONE_TRIES_LEFT = 0;

    /**
     * @expectedException \Hangman\Bundle\ApiBundle\Services\Exceptions\InvalidGameStatusException
     */
    public function testGameStatusFailed()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_FAIL);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $hangmanService->updateGame($game, $validLetter);
    }

    /**
     * @expectedException \Hangman\Bundle\ApiBundle\Services\Exceptions\InvalidGameStatusException
     */
    public function testGameStatusSuccess()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_SUCCESS);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $hangmanService->updateGame($game, $validLetter);
    }

    public function testGameStatusBusy()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $validLetter);
        $this->assertSame(
            $game->getStatus(),
            $currentGame->getStatus()
        );
    }

    /**
     * @expectedException \Hangman\Bundle\ApiBundle\Services\Exceptions\LetterNotValidException
     */
    public function testLetterInputNotValid()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);

        $invalidLetter = 'A';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $hangmanService->updateGame($game, $invalidLetter);
    }

    public function testLetterInputValid()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $validLetter);
        $this->assertSame(
            $game->getStatus(),
            $currentGame->getStatus()
        );
    }

    public function testLetterGuessedCorrect()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord(self::RANDOM_WORD);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $validLetter);
        $this->assertContains(
            $validLetter,
            $currentGame->getCharactersGuessed()
        );
    }

    public function testLetterGuessedInCorrect()
    {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord(self::RANDOM_WORD);

        $invalidLetter = 'q';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $invalidLetter);
        $this->assertNotContains(
            $invalidLetter,
            $currentGame->getCharactersGuessed()
        );
    }

    public function testTriesLeftIncreased()
    {
        $initialTriesLeft = Game::TRIES_LEFT;
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord(self::RANDOM_WORD);
        $game->setTriesLeft(Game::TRIES_LEFT);

        $invalidLetter = 'q';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $invalidLetter);
        $this->assertTrue($currentGame->getTriesLeft() < $initialTriesLeft);
    }

    public function testTriesLeftNotIncreased()
    {
        $initialTriesLeft = Game::TRIES_LEFT;
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord(self::RANDOM_WORD);
        $game->setTriesLeft(Game::TRIES_LEFT);

        $validLetter = 'a';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $validLetter);
        $this->assertEquals($currentGame->getTriesLeft(), $initialTriesLeft);
    }

    public function testGameFailed() {
        $game = new Game();
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord(self::RANDOM_WORD);
        $game->setTriesLeft(self::ONE_TRY_LEFT);

        $invalidLetter = 'q';

        $hangmanService = new HangmanGameService($this->getEntityManagerMock());
        $currentGame = $hangmanService->updateGame($game, $invalidLetter);
        $this->assertSame($currentGame->getStatus(), Game::STATUS_FAIL);

        $this->assertSame($currentGame->getTriesLeft(), self::NONE_TRIES_LEFT);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEntityManagerMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(array())
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}