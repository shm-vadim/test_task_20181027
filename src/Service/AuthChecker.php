<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class AuthChecker
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function isGranted(...$parameters)
    {
        try {
            return $this->authorizationChecker->isGranted(...$parameters);
        } catch (AuthenticationCredentialsNotFoundException $exception) {
            return false;
        }
    }
}
