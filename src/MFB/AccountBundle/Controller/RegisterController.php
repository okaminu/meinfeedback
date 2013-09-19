<?php

namespace MFB\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegisterController extends Controller
{
    public function indexAction()
    {
        return $this->render('MFBAccountBundle:Register:index.html.twig');
    }

}
