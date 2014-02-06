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
        return array('entity' => $entity, 'form'   => $this->createCreateForm($entity)->createView());
    }

    /**
     * @Route("/create", name="mfb_account_create")
     * @Method({"POST"})
     * @Template("MFBAccountBundle:Register:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $accountService = $this->get('mfb_account.service');

        $account = $accountService->createNew();
        $form = $this->createCreateForm($account);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $account = $this->get('mfb_account.security.service')->encryptAccountPassword($account);
            $accountService->store($account);

            return $this->redirect($this->get('mfb_payment.service')->getSignUrl($account->getId()));
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
