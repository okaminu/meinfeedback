<?php

namespace MFB\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ResetController extends Controller
{
    /**
     * @Route("/request", name="mfb_account_reset_request")
     * @Template
     */
    public function requestPasswordAction()
    {
        return array();
    }

    /**
     * @Route("/send", name="mfb_account_send_email")
     * @Template
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
        $account = $this->get('mfb_account.service')->findByEmail($username);
        if (null == $account) {
            return $this->render('MFBAccountBundle:Reset:request.html.twig', array('invalid_username' => $username));
        }
        $encoder = $this->get('security.encoder_factory')->getEncoder($account);
        $account->setSalt(base64_encode($this->get('security.secure_random')->nextBytes(20)));
        $newPassword = $this->container->get('mfb_account.util.token_generator')->generatePassword();
        $account->setPassword($encoder->encodePassword($newPassword, $account->getSalt()));

        $this->get('mfb_email.sender')->sendResettingEmailMessage($account, $newPassword);
        $this->get('mfb_account.service')->addAccount($account);

        return array();
    }


}
