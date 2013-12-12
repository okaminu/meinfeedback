<?php
namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FormSetupController extends Controller
{

    public function showAction()
    {
        return $this->render('MFBAdminBundle:Default:formSetup.html.twig');
    }
}
