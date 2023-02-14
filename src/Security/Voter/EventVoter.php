<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EVENT_EDIT', 'EVENT_DELETE', 'EVENT_ADD_MEMBER'])
            && $subject instanceof \App\Entity\Event;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EVENT_EDIT':
                // control if the event author is the user who want to edit the event
                if ($subject->getAuthor()->getId() === $user->getId()) {
                    return true;
                }
                break;
            case 'EVENT_DELETE':
                // control if the event author is the user who want to delete the event
                if ($subject->getAuthor()->getId() === $user->getId()) {
                    return true;
                }
                break;
            case 'EVENT_ADD_MEMBER':
                // control if event max members limit is not already reached and if user is not already a member
                if ($subject->getMembersCount() < $subject->getMaxMembers()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
