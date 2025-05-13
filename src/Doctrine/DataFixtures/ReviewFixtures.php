<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $generator,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $videoGames = $manager->getRepository(VideoGame::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($videoGames as $videoGame) {
            $shuffledUsers = $users;
            shuffle($shuffledUsers);

            $reviews = array_fill_callback(0, 10, fn (int $index): Review => (new Review())
                ->setComment($this->generator->text(200))
                ->setRating($this->generator->numberBetween(1, 5))
                ->setVideoGame($videoGame)
                ->setUser($shuffledUsers[$index])
            );

            array_walk($reviews, [$manager, 'persist']);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            VideoGameFixtures::class,
        ];
    }
}
