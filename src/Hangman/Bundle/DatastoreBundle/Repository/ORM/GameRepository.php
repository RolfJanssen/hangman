<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 31-10-14
 * Time: 15:50
 */

namespace Hangman\Bundle\DatastoreBundle\Repository\ORM;

use Doctrine\ORM\ORMException;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use RuntimeException;
use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{

    /**
     * @return array
     */
    public function startGame($randomWord)
    {
        $entityManager = $this->getEntityManager();

        $game = new Game();
        $game->setTriesLeft(Game::TRIES_LEFT);
        $game->setStatus(Game::STATUS_BUSY);
        $game->setWord($randomWord);

        $entityManager->persist($game);
        $entityManager->flush();

        return $game;
    }
}