<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 29-10-14
 * Time: 17:08
 */
namespace Hangman\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Hangman\Bundle\ApiBundle\Services\Exceptions\InvalidGameStatusException;
use Hangman\Bundle\ApiBundle\Services\Exceptions\LetterNotValidException;

use RuntimeException;

class GameController extends Controller
{
    /**
     * Starts a new game by creating a new game object and store it in the database
     * @return JsonResponse - jsonstring containing random word
     */
    public function gamesAction()
    {
        $wordRepository = $this->getDoctrine()->getManager()->getRepository('HangmanDatastoreBundle:ORM\Word');
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('HangmanDatastoreBundle:ORM\Game');
        $session = $this->get('session');

        try
        {
            $randomWord = $wordRepository->getRandomWord();
            $currentGame = $gameRepository->startGame($randomWord);

            $session->set('word', $randomWord);
            $session->set('currentGame', $currentGame->getId());
            $response = new JsonResponse(array("word" => $randomWord));
        }
        catch (RuntimeException $runtimeException)
        {
            $response = new JsonResponse(array("error" => array("code" => $runtimeException->getCode(), "message" => $runtimeException->getMessage())));
        }

        return $response;
    }

    /**
     * Check if given letter is part of the word of the current running game
     * @param Request $request
     * @return JsonResponse
     */
    public function guessLetterAction(Request $request)
    {
        $letter = $request->get('letter');
        $gameRepository = $this->getDoctrine()->getManager()->getRepository('HangmanDatastoreBundle:ORM\Game');
        $hangmanGameService = $this->get('hangman_game_service');
        $gameId = $this->get('session')->get('currentGame');

        try
        {
            $game = $gameRepository->find($gameId);
            $currentGame = $hangmanGameService->updateGame($game, $letter);
            $response = new JsonResponse(
                array("word" => $currentGame->getWord(), "tries_left" => $currentGame->getTriesLeft(), "status" => $currentGame->getStatus())
            );
        }
        catch (LetterNotValidException $letterNotValidException)
        {
            $response = new JsonResponse(
                array("status" => "error", "message" => $letterNotValidException->getMessage()),
                $letterNotValidException->getCode()
            );
        }
        catch (InvalidGameStatusException $invalidGameStatusException)
        {
            $response = new JsonResponse(
                array("status" => "error", "message" => $invalidGameStatusException->getMessage()),
                $invalidGameStatusException->getCode()
            );
        }
        catch (Exception $exception)
        {
            $response = new JsonResponse(
                array("status" => "error", "message" => $exception->getMessage()),
                $exception->getCode()
            );
        }

        return $response;
    }
}