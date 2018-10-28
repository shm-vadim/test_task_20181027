<?php

namespace App\Security\Voter;

use App\Service\AuthChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;
use App\Service\UserLoader;

class UserVoter extends Voter
{
    use BaseTrait;
    private $userLoader;
    private $authChecker;

    public function __construct(UserLoader $userLoader, AuthChecker $authChecker)
    {
        $this->userLoader = $userLoader;
        $this->authChecker = $authChecker;
    }

    protected function supports($attribute, $subject)
    {
        return $this->supportsUser($attribute, $subject);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $this->checkRight($attribute, $subject ?? $this->userLoader->getUser(), $token);
    }

    private function canCreateTransactions() : bool
    {
        return $this->authChecker->isGranted('ROLE_USER');
    }
}
