<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        for ($videoGames = [], $i = 0; $i < 50; ++$i) {
            $desc = implode($this->faker->paragraphs(10, false));
            $test = implode($this->faker->paragraphs(6, false));

            $videoGames[] = (new VideoGame())
                ->setTitle(sprintf('Jeu vidéo %d', $i))
                ->setDescription($desc)
                ->setReleaseDate(new \DateTimeImmutable())
                ->setTest($test)
                ->setRating(($i % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $i))
                ->setImageSize(2_098_872);

            $manager->persist($videoGames[$i]);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
