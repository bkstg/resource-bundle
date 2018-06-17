<?php

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

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $auth
    ) {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
           ProductionMenuCollectionEvent::NAME => array(
               array('addResourceItem', 0),
           )
        );
    }

    public function addResourceItem(ProductionMenuCollectionEvent $event)
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
