<?php

declare(strict_types=1);

namespace App\Doctrine\EntityListener;

use App\Model\Entity\User;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserListener
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[PrePersist]
    public function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        assert(
            null !== $plainPassword,
            'The plain password should not be null when hashing the password.'
        );

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
    }
}
