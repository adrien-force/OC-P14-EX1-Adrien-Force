<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $em = $this->service(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        return $em;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $id
     */
    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

    /**
     * @param array<string, string> $parameters
     */
    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    protected function login(string $email = 'user+0@email.com'): void
    {
        /**
         * @var EntityManagerInterface $service
         */
        $service = $this->service(EntityManagerInterface::class);
        $user = $service->getRepository(User::class)->findOneByEmail($email);

        if (!$user instanceof User) {
            self::fail(sprintf('User with email "%s" not found.', $email));
        }

        $this->client->loginUser($user);
    }
}
