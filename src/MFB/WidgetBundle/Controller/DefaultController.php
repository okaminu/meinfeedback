<?php

namespace MFB\WidgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($accountId)
    {
        $response = new Response();

        $widget = $this->get('mfb_widget.service')->createMainWidget($accountId);

        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($widget);
        return $response;
    }
}
