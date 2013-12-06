<?php

namespace MFB\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ResetController extends Controller
{
    public function requestPasswordAction()
    {
        return $this->render(
            'MFBAccountBundle:Reset:request.html.twig'
        );
    }

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

        return $this->render(
            'MFBAccountBundle:Reset:sendEmail.html.twig'
        );
    }


}
