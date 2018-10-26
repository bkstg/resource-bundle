<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Timeline\EventSubscriber;

use Bkstg\TimelineBundle\Event\TimelineLinkEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreatedResourceLinkSubscriber implements EventSubscriberInterface
{
    private $url_generator;

    /**
     * Create a new resource created listener.
     *
     * @param UrlGeneratorInterface $url_generator The url generator service.
     */
    public function __construct(UrlGeneratorInterface $url_generator)
    {
        $this->url_generator = $url_generator;
    }

    /**
     * Return the events this subscriber listens for.
     *
     * @return array The subscribed events.
     */
    public static function getSubscribedEvents()
    {
        return [
            TimelineLinkEvent::NAME => [
                ['setCreatedResourceLink', 0],
            ],
        ];
    }

    /**
     * Set the link for the created resource.
     *
     * @param TimelineLinkEvent $event The timeline link event.
     *
     * @return void
     */
    public function setCreatedResourceLink(TimelineLinkEvent $event): void
    {
        $action = $event->getAction();

        if ('created_resource' != $action->getVerb()) {
            return;
        }

        $production = $action->getComponent('indirectComplement')->getData();
        $resource = $action->getComponent('directComplement')->getData();
        $event->setLink($this->url_generator->generate('bkstg_resource_read', [
            'id' => $resource->getId(),
            'production_slug' => $production->getSlug(),
        ]));
    }
}
