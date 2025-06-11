<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class LoginTest extends FunctionalTestCase
{
    public function testThatLoginShouldSucceeded(): void
    {
        $this->get('/auth/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'testuser@gmail.com',
            'password' => 'password',
        ]);

        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);

        if (!$authorizationChecker instanceof AuthorizationCheckerInterface) {
            self::fail('AuthorizationCheckerInterface service is not available.');
        }

        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->get('/auth/logout');

        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    public function testThatLoginShouldFailed(): void
    {
        $this->get('/auth/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'testuser@gmail.com',
            'password' => 'fail',
        ]);

        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);

        if (!$authorizationChecker instanceof AuthorizationCheckerInterface) {
            self::fail('AuthorizationCheckerInterface service is not available.');
        }

        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }
}
