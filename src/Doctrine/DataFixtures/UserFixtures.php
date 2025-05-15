<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class UserFixtures extends Fixture
{
    public function __construct(private readonly Generator $faker)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = \array_fill_callback(0, 10, fn (int $index): User => (new User())
            ->setUsername($name = $this->faker->userName)
            ->setEmail(sprintf('%s@email.com', $name))
            ->setPlainPassword('password')
        );

        array_walk($users, [$manager, 'persist']);

        $manager->flush();
    }
}
