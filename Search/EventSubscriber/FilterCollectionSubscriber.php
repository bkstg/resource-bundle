<?php

namespace Bkstg\ResourceBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FilterCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterCollectionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FilterCollectionEvent::NAME => [
                ['addResourceFilter', 0],
            ]
        ];
    }

    public function addResourceFilter(FilterCollectionEvent $event)
    {
        $now = new \DateTime();
        $qb = $event->getQueryBuilder();
        $query = $qb->query()->bool()
            ->addMust($qb->query()->term(['_type' => 'resource']))
            ->addMust($qb->query()->term(['active' => true]))
            ->addMust($qb->query()->terms('groups.id', $event->getGroupIds()))
        ;
        $event->addFilter($query);
    }
}
