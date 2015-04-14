<?php
/**
 * Created by PhpStorm.
 * User: rolfjanssen
 * Date: 29-10-14
 * Time: 16:53
 */

namespace Hangman\DatastoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Word;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $kernel = $this->container->get('kernel');
        $fileContent = file_get_contents($kernel->locateResource('@HangmanDatastoreBundle/Resources/randomwordlist'));
        $randomWords = explode("\n", $fileContent);
        foreach ($randomWords as $randomWord) {
            $word = new Word();
            $word->setWord($randomWord);
            $manager->persist($word);
        }

        $manager->flush();
    }
}