<?php

namespace App\Security\Voter;

use App\Entity\User;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

trait BaseTrait
{
    protected $subject;

    protected function supportsUser($attribute, $subject): bool
    {
        return (($subject instanceof User) or null === $subject) && $this->hasHandler($attribute);
    }

    protected function checkRight($attribute, $subject, TokenInterface $token): bool
    {
        $this->subject = $subject;
        $handlerName = $this->getHandlerName($attribute);

        if (!method_exists($this, $handlerName)) {
            throw new \Exception(sprintf('%s has not %s priv handler, attempted to find %s method', self::class, $attribute, $handlerName));
        }

        return $this->$handlerName();
    }

    protected function hasHandler($attribute): bool
    {
        return method_exists($this, $this->getHandlerName($attribute));
    }

    private function hasPrefix($prefix, $attribute): bool
    {
        return (bool) preg_match("#^{$prefix}_#", $attribute);
    }

    private function getHandlerName($attribute): string
    {
        $prefix = 'can';

        if ($this->hasPrefix('IS', $attribute)) {
            $prefix = '';
        }

        if ($this->hasPrefix('PRIV', $attribute)) {
            $prefix = 'has';
        }

        return Inflector::camelize(mb_strtolower($prefix.'_'.$attribute));
    }
}
