<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Timeline\EventSubscriber;

use Bkstg\CoreBundle\Event\EntityPublishedEvent;
use Bkstg\ResourceBundle\Entity\Resource;
use Spy\Timeline\Driver\ActionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ResourceTimelineSubscriber implements EventSubscriberInterface
{
    private $action_manager;
    private $user_provider;

    /**
     * Create a new resource notification listener.
     *
     * @param ActionManagerInterface $action_manager The action manager service.
     * @param UserProviderInterface  $user_provider  The user provider service.
     */
    public function __construct(
        ActionManagerInterface $action_manager,
        UserProviderInterface $user_provider
    ) {
        $this->action_manager = $action_manager;
        $this->user_provider = $user_provider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityPublishedEvent::NAME => [
                ['createResourceTimelineEntry', 0],
            ],
        ];
    }

    public function createResourceTimelineEntry(EntityPublishedEvent $event): void
    {
        // Only act on resource objects.
        $resource = $event->getObject();
        if (!$resource instanceof Resource) {
            return;
        }

        // Get the author for the resource.
        $author = $this->user_provider->loadUserByUsername($resource->getAuthor());

        // Create components for this action.
        $resource_component = $this->action_manager->findOrCreateComponent($resource);
        $author_component = $this->action_manager->findOrCreateComponent($author);

        // Add timeline entries for each group.
        foreach ($resource->getGroups() as $group) {
            // Create the group component.
            $group_component = $this->action_manager->findOrCreateComponent($group);

            // Create the action and link it.
            $action = $this->action_manager->create($author_component, 'created_resource', [
                'directComplement' => $resource_component,
                'indirectComplement' => $group_component,
            ]);

            // Update the action.
            $this->action_manager->updateAction($action);
        }
    }
}
