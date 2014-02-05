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
        $entity = $this->get('mfb_account.service')->createNew();
        $form   = $this->createCreateForm($entity);

        return array('entity' => $entity, 'form'   => $form->createView());

    }

    /**
     * @Route("/create", name="mfb_account_create")
     * @Method({"POST"})
     * @Template("MFBAccountBundle:Register:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $account = $this->get('mfb_account.service')->createNew();
        $form = $this->createCreateForm($account);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('mfb_account.service')->store($account);
            $this->get('mfb_account.security.service')->login($account->getId(), 'secured_area');
            return $this->redirect($this->generateUrl('mfb_admin_homepage'));
        }

        return array('entity' => $account,'form'   => $form->createView());
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
