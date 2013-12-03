<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HelperController extends Controller
{
    public function getCompanyNameAction()
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $entity = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(array('accountId' => $accountId));

        return $this->render(
            'MFBAdminBundle:Helper:helper.html.twig',
            array('helperData' => $entity->getName())
        );
    }
}
