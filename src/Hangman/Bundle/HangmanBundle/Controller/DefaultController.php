<?php

namespace Hangman\Bundle\HangmanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('HangmanHangmanBundle:Default:index.html.twig', array());
    }
}
