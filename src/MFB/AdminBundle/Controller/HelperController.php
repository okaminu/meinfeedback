<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelperController extends Controller
{
    public function getAccountNameAction()
    {
        $entity = $this->get('mfb_account.service')->findByAccountId($this->getUserId());
        return $this->showText($entity->getName());
    }

    private function showText($text)
    {
        return $this->render(
            'MFBAdminBundle:Helper:helper.html.twig',
            array('helperData' => $text)
        );
    }

    private function getUserId()
    {
        $token = $this->get('security.context')->getToken();
        return $token->getUser()->getId();
    }

}
