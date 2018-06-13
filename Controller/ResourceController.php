<?php

namespace Bkstg\ResourceBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    public function indexAction(
        PaginatorInterface $paginator,
        Request $request
    ) {
        return new Response($this->twig->render('@BkstgResource/Resource/index.html.twig'));
    }

    public function createAction(Request $request) {
        return new Response($this->twig->render('@BkstgResource/Resource/create.html.twig'));
    }

    public function readAction($id, $production_slug) {
        return new Response($this->twig->render('@BkstgResource/Resource/read.html.twig'));
    }

    public function updateAction($id, $production_slug, Request $request) {
        return new Response($this->twig->render('@BkstgResource/Resource/update.html.twig'));
    }

    public function deleteAction($id, $production_slug, Request $request) {
        return new Response($this->twig->render('@BkstgResource/Resource/delete.html.twig'));
    }
}
