<?php

namespace MeinFeedback\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MeinFeedbackHomeBundle:Default:index.html.twig', array('name' => ''));
    }
}
