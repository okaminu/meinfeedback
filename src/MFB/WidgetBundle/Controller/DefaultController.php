<?php

namespace MFB\WidgetBundle\Controller;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\WidgetBundle\Builder\ImageBuilder;
use MFB\WidgetBundle\Director\MainWidgetDirector;
use MFB\WidgetBundle\Entity\Color;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MFB\FeedbackBundle\Specification as Spec;
use MFB\WidgetBundle\Entity\Widget as WidgetEntity;

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
