<?php

namespace MFB\AccountBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\AccountBundle\Form\Type\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    public function indexAction()
    {
        $entity = new Account();
        $form   = $this->createCreateForm($entity);

        return $this->render('MFBAccountBundle:Register:index.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));

    }

    public function createAction(Request $request)
    {
        $entity = new Account();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $entity->setSalt(base64_encode($this->get('security.secure_random')->nextBytes(20)));
            $entity->setPassword($encoder->encodePassword($entity->getPassword(), $entity->getSalt()));
            $entity->setIsEnabled(false);
            $entity->setIsLocked(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('mfb_account.security.service')->login($entity->getId(), 'secured_area');

            $url = $this->get('mfb_reskribe.api')->getSignUrl($entity);
            return $this->redirect($url);
        }

        return $this->render('MFBAccountBundle:Register:index.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
    }

    private function createCreateForm(Account $entity)
    {
        $form = $this->createForm(new AccountType(), $entity, array(
                'action' => $this->generateUrl('mfb_account_create'),
                'method' => 'POST',
            ));

        return $form;
    }
}
