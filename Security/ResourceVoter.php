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

    public function canEdit(GroupableInterface $resource, TokenInterface $token)
    {
        $user = $token->getUser();
        foreach ($resource->getGroups() as $group) {
            if ($this->decision_manager->decide($token, ['GROUP_ROLE_EDITOR'], $group)) {
                return true;
            }
        }
        return false;
    }
}
