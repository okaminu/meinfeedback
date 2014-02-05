<?php

namespace MFB\WidgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/account_channel/{accountId}", name="mfb_widget_account_channel")
     */
    public function indexAction($accountId)
    {
        $response = new Response();

        $widget = $this->get('mfb_widget.service')->createMainWidget($accountId);

        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($widget);
        return $response;
    }
}
