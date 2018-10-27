<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserLoader
{
    private $user;
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->user = $this->getUserFromToken();
    }

    public function getUser() : ? UserInterface
    {
        return $this->user;
    }

    public function hasUser() : bool
    {
        return !($this->user instanceof UserInterface);
    }

    private function getUserFromToken() : ? UserInterface
    {
        if ($token = $this->tokenStorage->getToken()) {
            return $token->getUser();
        }

        return null;
    }
}
