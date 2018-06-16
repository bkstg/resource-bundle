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
    public function indexAction(
        $production_slug,
        PaginatorInterface $paginator,
        AuthorizationCheckerInterface $auth,
        Request $request
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        $resource_repo = $this->em->getRepository(Resource::class);
        $query = $resource_repo->findAllByGroupQuery($production);
        $resources = $paginator->paginate($query, $request->query->getInt('page', 1));

        return new Response($this->templating->render(
            '@BkstgResource/Resource/index.html.twig', [
                'resources' => $resources,
                'production' => $production,
            ]
        ));
    }

    public function archiveAction(
        $production_slug,
        PaginatorInterface $paginator,
        AuthorizationCheckerInterface $auth,
        Request $request
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('GROUP_ROLE_EDITOR', $production)) {
            throw new AccessDeniedException();
        }

        $resource_repo = $this->em->getRepository(Resource::class);
        $query = $resource_repo->findAllByGroupQuery($production, false);
        $resources = $paginator->paginate($query, $request->query->getInt('page', 1));

        return new Response($this->templating->render(
            '@BkstgResource/Resource/archive.html.twig', [
                'resources' => $resources,
                'production' => $production,
            ]
        ));
    }

    public function createAction(
        $production_slug,
        TokenStorageInterface $token,
        AuthorizationCheckerInterface $auth,
        Request $request
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('GROUP_ROLE_EDITOR', $production)) {
            throw new AccessDeniedException();
        }

        $user = $token->getToken()->getUser();

        $resource = new Resource();
        $resource->setAuthor($user->getUsername());
        $resource->setStatus(true);
        $resource->setPinned(false);
        $resource->addGroup($production);

        $form = $this->form->create(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($resource);
            $this->em->flush();

            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('resource.created', [], 'BkstgResourceBundle')
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_show', [
                    'id' => $resource->getId(),
                    'production_slug' => $production->getSlug(),
                ]
            ));
        }
        return new Response($this->templating->render(
            '@BkstgResource/Resource/create.html.twig', [
                'form' => $form->createView(),
                'production' => $production,
            ]
        ));
    }

    public function readAction(
        $id,
        $production_slug,
        AuthorizationCheckerInterface $auth
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        $resource_repo = $this->em->getRepository(Resource::class);
        if (null === $resource = $resource_repo->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if (!$resource->hasGroup($production)) {
            throw new NotFoundHttpException();
        }

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

    public function updateAction(
        $id,
        $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        $resource_repo = $this->em->getRepository(Resource::class);
        if (null === $resource = $resource_repo->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if (!$resource->hasGroup($production)) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('edit', $resource)) {
            throw new AccessDeniedException();
        }

        $form = $this->form->create(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('resource.updated', ['%name%' => $resource->getName()], 'BkstgResourceBundle')
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_show', [
                    'id' => $resource->getId(),
                    'production_slug' => $production->getSlug(),
                ]
            ));
        }
        return new Response($this->templating->render(
            '@BkstgResource/Resource/update.html.twig', [
                'resource' => $resource,
                'form' => $form->createView(),
                'production' => $production,
            ]
        ));
    }

    public function deleteAction(
        $id,
        $production_slug,
        AuthorizationCheckerInterface $auth,
        Request $request
    ) {
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        $resource_repo = $this->em->getRepository(Resource::class);
        if (null === $resource = $resource_repo->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if (!$resource->hasGroup($production)) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('edit', $resource)) {
            throw new AccessDeniedException();
        }

        $form = $this->form->createBuilder()
            ->add('id', HiddenType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($resource);
            $this->em->flush();

            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('Deleted resource "%name%".', [
                    '%name%' => $resource->getName(),
                ])
            );

            // Redirect to event index.
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_resource_index',
                ['production_slug' => $production->getSlug()]
            ));
        }
        return new Response($this->templating->render(
            '@BkstgResource/Resource/delete.html.twig', [
                'resource' => $resource,
                'production' => $production,
                'form' => $form->createView(),
            ]
        ));
    }
}
