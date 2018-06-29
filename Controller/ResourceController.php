<?php

namespace Bkstg\ResourceBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\ResourceBundle\Entity\Resource;
use Bkstg\ResourceBundle\Form\ResourceType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResourceController extends Controller
{
    /**
     * Show a list of resources for a production.
     *
     * @param  string                        $production_slug The production slug.
     * @param  PaginatorInterface            $paginator       The paginator service.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  Request                       $request         The incoming request.
     * @throws NotFoundHttpException When the production is not found.
     * @throws AccessDeniedException When the user is not a member of the production.
     * @return Response
     */
    public function indexAction(
        string $production_slug,
        PaginatorInterface $paginator,
        AuthorizationCheckerInterface $auth,
        Request $request
    ): Response {
        // Lookup the production.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Ensure the user is a member of the production.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        // Get active resources.
        $resource_repo = $this->em->getRepository(Resource::class);
        $query = $resource_repo->findAllByGroupQuery($production);

        // Paginate and render result.
        $resources = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render(
            '@BkstgResource/Resource/index.html.twig',
            [
                'resources' => $resources,
                'production' => $production,
            ]
        ));
    }

    /**
     * Show a list of archived resources for a production.
     *
     * @param  string                        $production_slug The production slug.
     * @param  PaginatorInterface            $paginator       The paginator service.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  Request                       $request         The incoming request.
     * @throws NotFoundHttpException When the production is not found.
     * @throws AccessDeniedException When the user is not a member of the production.
     * @return Response
     */
    public function archiveAction(
        string $production_slug,
        PaginatorInterface $paginator,
        AuthorizationCheckerInterface $auth,
        Request $request
    ): Response {
        // Lookup the production.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Ensure the user is a member of the production.
        if (!$auth->isGranted('GROUP_ROLE_EDITOR', $production)) {
            throw new AccessDeniedException();
        }

        // Get inactive resources.
        $resource_repo = $this->em->getRepository(Resource::class);
        $query = $resource_repo->findAllByGroupQuery($production, false);

        // Paginate and render result.
        $resources = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render(
            '@BkstgResource/Resource/archive.html.twig',
            [
                'resources' => $resources,
                'production' => $production,
            ]
        ));
    }

    /**
     * Create a new resource in a production.
     *
     * @param  string                        $production_slug The production slug.
     * @param  TokenStorageInterface         $token           The token storage service.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  Request                       $request         The incoming request.
     * @throws NotFoundHttpException When the production is not found.
     * @throws AccessDeniedException When the user is not a member of the production.
     * @return Response
     */
    public function createAction(
        string $production_slug,
        TokenStorageInterface $token,
        AuthorizationCheckerInterface $auth,
        Request $request
    ): Response {
        // Lookup the production.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Ensure the user is a member of the production.
        if (!$auth->isGranted('GROUP_ROLE_EDITOR', $production)) {
            throw new AccessDeniedException();
        }

        // Get some information about the user.
        $user = $token->getToken()->getUser();

        // Create a new active resource.
        $resource = new Resource();
        $resource->setAuthor($user->getUsername());
        $resource->setActive(true);
        $resource->setPinned(false);
        $resource->addGroup($production);

        // Create and handle the form.
        $form = $this->form->create(ResourceType::class, $resource);
        $form->handleRequest($request);

        // Form is valid, persist and flush.
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($resource);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('resource.created', [], 'BkstgResourceBundle')
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_show',
                [
                    'id' => $resource->getId(),
                    'production_slug' => $production->getSlug(),
                ]
            ));
        }

        // Render the form.
        return new Response($this->templating->render(
            '@BkstgResource/Resource/create.html.twig',
            [
                'form' => $form->createView(),
                'production' => $production,
            ]
        ));
    }

    /**
     * Read a resource from this production.
     *
     * @param  integer                       $id              The resource id.
     * @param  string                        $production_slug The production slug.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @throws AccessDeniedException When the user does not have access to view the resource.
     * @return Response
     */
    public function readAction(
        int $id,
        string $production_slug,
        AuthorizationCheckerInterface $auth
    ): Response {
        list($resource, $production) = $this->lookupEntity(Resource::class, $id, $production_slug);

        if (!$auth->isGranted('view', $resource)) {
            throw new AccessDeniedException();
        }

        return new Response($this->templating->render(
            '@BkstgResource/Resource/read.html.twig', [
                'production' => $production,
                'resource' => $resource,
            ]
        ));
    }

    /**
     * Update a resource for this production.
     *
     * @param  integer                       $id              The resource id.
     * @param  string                        $production_slug The production slug.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @throws AccessDeniedException When the user does not have access to edit the resource.
     * @return Response
     */
    public function updateAction(
        int $id,
        string $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth
    ): Response {
        // Lookup the resource and production.
        list($resource, $production) = $this->lookupEntity(Resource::class, $id, $production_slug);

        // Check access to the resource.
        if (!$auth->isGranted('edit', $resource)) {
            throw new AccessDeniedException();
        }

        // Create and handle the form.
        $form = $this->form->create(ResourceType::class, $resource);
        $form->handleRequest($request);

        // Form is submitted, flush changes.
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('resource.updated', ['%name%' => $resource->getName()], 'BkstgResourceBundle')
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_show',
                [
                    'id' => $resource->getId(),
                    'production_slug' => $production->getSlug(),
                ]
            ));
        }

        // Render the response.
        return new Response($this->templating->render(
            '@BkstgResource/Resource/update.html.twig',
            [
                'resource' => $resource,
                'form' => $form->createView(),
                'production' => $production,
            ]
        ));
    }

    /**
     * Delete a resource for this production.
     *
     * @param  integer                       $id              The resource id.
     * @param  string                        $production_slug The production slug.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  Request                       $request         The incoming request.
     * @throws AccessDeniedException When the user does not have access to edit the resource.
     * @return Response
     */
    public function deleteAction(
        int $id,
        string $production_slug,
        AuthorizationCheckerInterface $auth,
        Request $request
    ): Response {
        // Lookup the resource and production.
        list($resource, $production) = $this->lookupEntity(Resource::class, $id, $production_slug);

        // Check access to the resource.
        if (!$auth->isGranted('edit', $resource)) {
            throw new AccessDeniedException();
        }

        // Create and handle the form.
        $form = $this->form->createBuilder()->getForm();
        $form->handleRequest($request);

        // Form is submitted, remove and flush.
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($resource);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('resource.deleted', [
                    '%name%' => $resource->getName(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_index',
                ['production_slug' => $production->getSlug()]
            ));
        }

        // Render the form.
        return new Response($this->templating->render(
            '@BkstgResource/Resource/delete.html.twig', [
                'resource' => $resource,
                'production' => $production,
                'form' => $form->createView(),
            ]
        ));
    }
}
