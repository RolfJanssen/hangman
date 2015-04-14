<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 14-4-15
 * Time: 12:28
 */

namespace Hangman\Bundle\HangmanBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testGames()
    {
        $client = static::createClient();

        $client->request('POST', '/games');
        $response = $client->getResponse();

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );

        $this->assertTrue($response->isSuccessful());
    }
}