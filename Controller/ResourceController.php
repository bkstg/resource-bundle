<?php

namespace Bkstg\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation;
use Bkstg\ResourceBundle\Entity\Resource;
use Bkstg\ResourceBundle\Form\ResourceType;
use Bkstg\CoreBundle\Manager\MessageManager;

/**
 * @Route("/resources")
 */
class ResourceController extends Controller
{
    /**
     * @Route("/", name="bkstg_resource_home")
     */
    public function indexAction(HttpFoundation\Request $request)
    {
        // get current user and entity manager
        $em = $this->getDoctrine()->getManager();

        // get resources
        $dql = "SELECT r FROM BkstgResourceBundle:Resource r ORDER BY r.created DESC";
        $query = $em->createQuery($dql);

        $form = $this->createForm(new ResourceType(), new Resource(), array(
            'action' => $this->generateUrl('bkstg_resource_add_resource'),
            'em' => $this->getDoctrine()->getManager(),
        ));

        // paginate
        $paginator = $this->get('knp_paginator');
        $resources = $paginator->paginate($query, $request->query->getInt('page', 1), 100);

        // get message manager
        $message_manager = $this->get('message.manager');

        return $this->render('BkstgResourceBundle:Resources:resources.html.twig', array(
            'resources' => $resources,
            'form' => $form->createView(),
            'message_manager' => $message_manager,
        ));
    }

    /**
     * @Route("/add", name="bkstg_resource_add_resource")
     */
    public function addAction(HttpFoundation\Request $request)
    {
        // only editors can add these
        $this->denyAccessUnlessGranted('ROLE_EDITOR');

        // get current user and entity manager
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        // create resource and form
        $resource = new Resource();
        $form = $this->createForm(new ResourceType(), $resource, array(
            'em' => $this->getDoctrine()->getManager(),
        ));

        // handle form and set user after (ensures this user isn't impersonated)
        $form->handleRequest($request);
        $resource->setUser($user);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resource);
            $em->flush();

            $this->addFlash(
                'success',
                'New resource added!'
            );

            return $this->redirectToRoute('bkstg_resource_home');
        }

        // get message manager
        $message_manager = $this->get('message.manager');

        return $this->render('BkstgCoreBundle:Generic:form.html.twig', array(
            'title' => 'Add new resource',
            'description' => 'Use the form below to add a new resource.',
            'form' => $form->createView(),
            'message_manager' => $message_manager,
        ));

    }

    /**
     * @Route("/view/{resource}", name="bkstg_resource_view_resource")
     * @ParamConverter("resource", class="BkstgResourceBundle:Resource")
     */
    public function viewAction(Resource $resource)
    {
        $file = new File($resource->getAbsolutePath());
        $headers = array(
            'Content-Type' => $file->getMimeType(),
            'Content-Disposition' => 'inline; filename="' . $resource->getPath() . '"'
        );

        $filename = $resource->getAbsolutePath();

        return new HttpFoundation\Response(file_get_contents($filename), 200, $headers);
    }

    /**
     * @Route("/download/{resource}", name="bkstg_resource_download_resource")
     * @ParamConverter("resource", class="BkstgResourceBundle:Resource")
     */
    public function downloadAction(Resource $resource)
    {
        $file = new File($resource->getAbsolutePath());
        $headers = array(
            'Content-Type' => $file->getMimeType(),
            'Content-Disposition' => 'attachment; filename="' . $resource->getPath() . '"'
        );

        $filename = $resource->getAbsolutePath();

        return new HttpFoundation\Response(file_get_contents($filename), 200, $headers);
    }

    /**
     * @Route("/delete/{resource}", name="bkstg_resource_delete_resource")
     * @ParamConverter("resource", class="BkstgResourceBundle:Resource")
     */
    public function deleteAction(Resource $resource)
    {
        // check this user has access
        $this->denyAccessUnlessGranted('edit', $resource, 'Unauthorized access!');

        // get entity manager
        $em = $this->getDoctrine()->getManager();

        $em->remove($resource);
        $em->flush();

        // redirect to board
        return $this->redirectToRoute('bkstg_resource_home');
    }
}
