<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MFBAdminBundle:Default:index.html.twig');
    }
    public function locationAction()
    {
        return $this->render('MFBAdminBundle:Default:location.html.twig');
    }
    public function customerAction()
    {
        return $this->render('MFBAdminBundle:Default:customer.html.twig');
    }
}
