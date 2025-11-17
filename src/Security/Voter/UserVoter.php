<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const VIEW   = 'USER_VIEW';
    public const EDIT   = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';
    public const CREATE = 'USER_CREATE';

    protected function supports(string $attribute, $subject): bool
    {
        // CREATE does not require a specific User object
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        // Root has full access
        if (in_array(UserRole::ROOT->value, $currentUser->getRoles())) {
            return true;
        }

        // User has limited access
        if (in_array(UserRole::USER->value, $currentUser->getRoles())) {
            switch ($attribute) {
                case self::VIEW:
                case self::EDIT:
                    // only self profile
                    return $currentUser->getId() === $subject->getId();

                case self::DELETE:
                    // user cannot delete
                    return false;

                case self::CREATE:
                    // user cannot create new users
                    return false;
            }
        }

        return false;
    }
}
