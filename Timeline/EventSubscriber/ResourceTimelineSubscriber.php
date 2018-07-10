<?php

namespace Bkstg\ResourceBundle\Timeline\EventSubscriber;

use Bkstg\CoreBundle\Event\EntityPublishedEvent;
use Bkstg\ResourceBundle\Entity\Resource;
use Spy\Timeline\Driver\ActionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ResourceTimelineSubscriber implements EventSubscriberInterface
{
    private $action_manager;
    private $user_provider;
    private $url_genertor;

    /**
     * Create a new resource notification listener.
     *
     * @param ActionManagerInterface $action_manager The action manager service.
     * @param UserProviderInterface  $user_provider  The user provider service.
     * @param UrlGeneratorInterface  $url_generator  The url generator service.
     */
    public function __construct(
        ActionManagerInterface $action_manager,
        UserProviderInterface $user_provider,
        UrlGeneratorInterface $url_generator
    ) {
        $this->action_manager = $action_manager;
        $this->user_provider = $user_provider;
        $this->url_generator = $url_generator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityPublishedEvent::NAME => [
                ['createResourceTimelineEntry', 0],
            ]
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
            $action->setLink($this->url_generator->generate('bkstg_resource_read', [
                'production_slug' => $group->getSlug(),
                'id' => $resource->getId(),
            ]));

            // Update the action.
            $this->action_manager->updateAction($action);
        }
    }
}