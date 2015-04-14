<?php

namespace Hangman\Bundle\HangmanBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("Hangman")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Start game")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("reset game")')->count() > 0);
    }
}
