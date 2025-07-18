<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $videogames = $manager->getRepository(VideoGame::class)->findAll();

        $tagNames = [
            'Action',
            'Adventure',
            'RPG',
            'Simulation',
            'Strategy',
            'Puzzle',
            'Sports',
            'Racing',
            'Shooter',
            'Platformer',
        ];

        $tags = array_map(
            static fn (string $name) => (new Tag())->setName($name),
            $tagNames
        );

        array_walk(
            $videogames,
            static function (VideoGame $videoGame) use ($tags) {
                $randomCount = random_int(1, 5);
                for ($i = 0; $i < $randomCount; ++$i) {
                    $videoGame->addTag($tags[array_rand($tags)]);
                }
            }
        );

        array_walk($tags, [$manager, 'persist']);
        array_walk($videogames, [$manager, 'persist']);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [VideoGameFixtures::class];
    }
}
