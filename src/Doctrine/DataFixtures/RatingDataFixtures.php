<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RatingDataFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly RatingHandler $ratingHandler,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $videogames = $manager->getRepository(VideoGame::class)->findAll();

        foreach ($videogames as $videogame) {
            $this->ratingHandler->calculateAverage($videogame);
            $this->ratingHandler->countRatingsPerValue($videogame);
        }
        array_walk($videogames, [$manager, 'persist']);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            VideoGameFixtures::class,
            ReviewFixtures::class,
        ];
    }
}
