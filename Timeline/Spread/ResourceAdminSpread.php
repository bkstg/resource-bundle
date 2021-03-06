<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Timeline\Spread;

use Bkstg\ResourceBundle\Entity\Resource;
use Bkstg\TimelineBundle\Spread\AdminSpread;
use Spy\Timeline\Model\ActionInterface;

class ResourceAdminSpread extends AdminSpread
{
    /**
     * {@inheritdoc}
     *
     * @param ActionInterface $action The action.
     *
     * @return bool
     */
    public function supports(ActionInterface $action)
    {
        // Only supports new resources.
        $object = $action->getComponent('directComplement')->getData();
        if (!$object instanceof Resource) {
            return false;
        }

        return true;
    }
}
