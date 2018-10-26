<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\EventSubscriber;

use Bkstg\CoreBundle\Event\ProductionMenuCollectionEvent;
use Bkstg\ResourceBundle\BkstgResourceBundle;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductionMenuSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $auth;

    /**
     * Create a new production menu subscriber.
     *
     * @param FactoryInterface              $factory The menu factory service.
     * @param AuthorizationCheckerInterface $auth    The authorization checker service.
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $auth
    ) {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    /**
     * Return the events this subscriber listens for.
     *
     * @return array The subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [
           ProductionMenuCollectionEvent::NAME => [
               ['addResourceItem', 0],
           ],
        ];
    }

    /**
     * Add the resource menu items.
     *
     * @param ProductionMenuCollectionEvent $event The menu collection event.
     *
     * @return void
     */
    public function addResourceItem(ProductionMenuCollectionEvent $event): void
    {
        $menu = $event->getMenu();
        $group = $event->getGroup();

        // Create resource menu item.
        $resources = $this->factory->createItem('menu_item.resources', [
            'route' => 'bkstg_resource_index',
            'routeParameters' => ['production_slug' => $group->getSlug()],
            'extras' => [
                'icon' => 'file',
                'translation_domain' => BkstgResourceBundle::TRANSLATION_DOMAIN,
            ],
        ]);
        $menu->addChild($resources);

        // If this user is an editor create the post and archive items.
        if ($this->auth->isGranted('GROUP_ROLE_EDITOR', $group)) {
            $resources_resources = $this->factory->createItem('menu_item.resources_resources', [
                'route' => 'bkstg_resource_index',
                'routeParameters' => ['production_slug' => $group->getSlug()],
                'extras' => ['translation_domain' => BkstgResourceBundle::TRANSLATION_DOMAIN],
            ]);
            $resources->addChild($resources_resources);

            $archive = $this->factory->createItem('menu_item.resources_archive', [
                'route' => 'bkstg_resource_archive',
                'routeParameters' => ['production_slug' => $group->getSlug()],
                'extras' => ['translation_domain' => BkstgResourceBundle::TRANSLATION_DOMAIN],
            ]);
            $resources->addChild($archive);
        }
    }
}
