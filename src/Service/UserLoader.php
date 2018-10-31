<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserLoader
{
    private $user;
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->user = $this->getUserFromToken();
    }

    public function getUser(): ? UserInterface
    {
        return $this->user;
    }

    public function hasUser(): bool
    {
        return $this->isUser($this->user);
    }

    private function isUser($user): bool
    {
        return $user instanceof UserInterface;
    }

    private function getUserFromToken(): ? UserInterface
    {
        if ($token = $this->tokenStorage->getToken()) {
            $user = $token->getUser();

            return $this->isUser($user) ? $user : null;
        }

        return null;
    }
}
