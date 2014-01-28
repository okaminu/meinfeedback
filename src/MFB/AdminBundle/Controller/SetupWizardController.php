<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SetupWizardController extends Controller
{
    public function stepAction()
    {
        return $this->render("MFBAdminBundle:SetupWizard:step.html.twig");
    }

}
