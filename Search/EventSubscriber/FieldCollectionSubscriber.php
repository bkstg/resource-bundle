<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FieldCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FieldCollectionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FieldCollectionEvent::NAME => [
                ['addResourceFields', 0],
            ],
        ];
    }

    public function addResourceFields(FieldCollectionEvent $event): void
    {
        $event->addFields([
            'name',
            'description',
            'author',
            'media.name',
        ]);
    }
}
