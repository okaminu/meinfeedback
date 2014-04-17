<?php

namespace MFB\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("/login", name="mfb_account_login")
     * @Route("/login_check", name="mfb_account_login_check")
     * @Template
     */
    public function indexAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $payUrl = '';
        if ($error && $error instanceof DisabledException) {
            $payUrl = $this->get('mfb_payment.service')->getSignUrl($error->getUser()->getId());
        }

        return array(
                'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'pay_url'       => $payUrl,
        );
    }
}
