<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Search\EventSubscriber;

use Bkstg\SearchBundle\Event\FilterCollectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * Return the events this subscriber listens for.
     *
     * @return array The subscribed events.
     */
    public static function getSubscribedEvents()
    {
        return [
            FilterCollectionEvent::NAME => [
                ['addResourceFilter', 0],
            ],
        ];
    }

    /**
     * Add the resource filter to search.
     *
     * @param FilterCollectionEvent $event The filter collection event.
     *
     * @return void
     */
    public function addResourceFilter(FilterCollectionEvent $event): void
    {
        $now = new \DateTime();
        $qb = $event->getQueryBuilder();
        $query = $qb->query()->bool()
            ->addMust($qb->query()->term(['_index' => 'resource']))
            ->addMust($qb->query()->term(['active' => true]))
            ->addMust($qb->query()->terms('groups.id', $event->getGroupIds()))
        ;
        $event->addFilter($query);
    }
}
