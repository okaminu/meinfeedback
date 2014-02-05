<?php

namespace MFB\AccountBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\AccountBundle\Form\Type\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="mfb_account_register")
     * @Template
     */
    public function indexAction()
    {
        $entity = new Account();
        $form   = $this->createCreateForm($entity);

        return array(
                'entity' => $entity,
                'form'   => $form->createView(),
            );

    }

    /**
     * @Route("/create", name="mfb_account_create")
     * @Method({"POST"})
     * @Template("MFBAccountBundle:Register:index.html.twig")
     */
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

        return array(
                'entity' => $entity,
                'form'   => $form->createView(),
            );
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
