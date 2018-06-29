<?php

namespace Bkstg\ResourceBundle\Security;

use Bkstg\CoreBundle\Security\GroupableEntityVoter;
use Bkstg\ResourceBundle\Entity\Resource;
use MidnightLuke\GroupSecurityBundle\Model\GroupableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ResourceVoter extends GroupableEntityVoter
{
    /**
     * {@inheritdoc}
     *
     * @param  mixed $attribute The attribute to vote on.
     * @param  mixed $subject   The subject to vote on.
     * @return boolean
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on Groupable objects inside this voter
        if (!$subject instanceof Resource) {
            return false;
        }

        return true;
    }
}
