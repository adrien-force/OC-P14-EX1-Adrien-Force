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
        for ($users = [], $i = 0; $i < 10; $i++) {
            $users[] = (new User())
                ->setUsername($name = $this->faker->userName)
                ->setEmail(sprintf('%s@email.com', $name))
                ->setPlainPassword('password');
        }

        $users[] = (new User())
            ->setUsername('testuser')
            ->setEmail('testuser@gmail.com')
            ->setPlainPassword('password');

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }
}