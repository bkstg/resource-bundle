<?php

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
            ]
        ];
    }

    public function addResourceFields(FieldCollectionEvent $event)
    {
        $event->addFields([
            'name',
            'description',
            'author',
            'media.filename',
        ]);
    }
}
