<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterTest extends FunctionalTestCase
{
    public function testThatRegistrationShouldSucceeded(): void
    {
        $this->get('/auth/logout');
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', self::getFormData());

        self::assertResponseRedirects('/auth/login');

        $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail('usertest1@email.com');

        $userPasswordHasher = $this->service(UserPasswordHasherInterface::class);

        if (!$userPasswordHasher instanceof UserPasswordHasherInterface) {
            self::fail('UserPasswordHasherInterface service is not available.');
        }

        self::assertNotNull($user);
        self::assertSame('usernametest1', $user->getUsername());
        self::assertSame('usertest1@email.com', $user->getEmail());
        self::assertTrue($userPasswordHasher->isPasswordValid($user, 'SuperPassword123!'));
    }

    /**
     * @dataProvider provideInvalidFormData
     * @param array<string, string> $formData
     */
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', $formData);

        self::assertResponseIsUnprocessable();
    }

    public static function provideInvalidFormData(): \Generator
    {
        yield 'empty username' => [self::getInvalidFormData(null, 'usertest1@email.com', 'SuperPassword123!')];
        yield 'non unique username' => [self::getInvalidFormData('testuser', 'usertest1@email.com', 'SuperPassword123!')];
        yield 'too long username' => [self::getInvalidFormData('Lorem ipsum dolor sit amet orci aliquam', 'usertest1@email.com', 'SuperPassword123!')];
        yield 'empty email' => [self::getInvalidFormData('usernametest1', null, 'SuperPassword123!')];
        yield 'non unique email' => [self::getInvalidFormData('usernametest1', 'testuser@gmail.com', 'SuperPassword123!')];
        yield 'invalid email' => [self::getInvalidFormData('usernametest1', 'fail', 'SuperPassword123!')];
    }

    /**
     * @return string[]
     */
    public static function getFormData(): array
    {
        return [
            'register[username]' => 'usernametest1',
            'register[email]' => 'usertest1@email.com',
            'register[plainPassword]' => 'SuperPassword123!',
        ];
    }

    /**
     * @param string|null $username
     * @param string|null $email
     * @param string|null $password
     * @return string[]
     */
    public static function getInvalidFormData(?string $username = null, ?string $email = null, ?string $password = null): array
    {
        return [
            'register[username]' => $username ?? '',
            'register[email]' => $email ?? '',
            'register[plainPassword]' => $password ?? ''
        ];
    }
}
