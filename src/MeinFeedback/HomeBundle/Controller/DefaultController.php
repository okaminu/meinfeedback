<?php

namespace MeinFeedback\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="mein_feedback_home_homepage")
     * @Template
     */
    public function indexAction()
    {
        return array('name' => '');
    }
}
