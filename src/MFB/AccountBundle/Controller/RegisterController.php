<?php

namespace MFB\AccountBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\AccountBundle\Form\Type\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller
{
    public function indexAction()
    {
        $entity = new Account();
        $form   = $this->createCreateForm($entity);

        $test = $form->createView();
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
            $entity->setIsEnabled(true);
            $entity->setIsLocked(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('mfb_account_channel.service')->createStoreNewChannel($entity->getId());

            return $this->redirect($this->generateUrl('mfb_account_login'));
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
