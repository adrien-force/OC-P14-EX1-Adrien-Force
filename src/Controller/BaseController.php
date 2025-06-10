<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    #[\Override]
    protected function getUser(): User
    {
        $user = parent::getUser();
        if ($user instanceof User) {
            return $user;
        }

        throw $this->createAccessDeniedException('You must be logged in to access this resource.');
    }
}
