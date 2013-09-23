<?php

namespace MFB\AdminBundle\Controller;

use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MFBAdminBundle:Default:index.html.twig');
    }
    public function locationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $entity = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(array('accountId'=>$accountId));
        if (!$entity) {
            $entity = new AccountChannel();
            $entity->setAccountId($accountId);
        }

        $form = $this->createForm(new AccountChannelType(), $entity, array(
                'action' => $this->generateUrl('mfb_location'),
                'method' => 'POST',
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mfb_location', array('id' => $entity->getId())));
        }

        return $this->render('MFBAdminBundle:Default:location.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
    }

    public function customerAction()
    {
        return $this->render('MFBAdminBundle:Default:customer.html.twig');
    }
}
