<?php

namespace MFB\ReskribeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/callback", name="mfb_reskribe_homepage")
     * @Method({"POST"})
     */
    public function indexAction(Request $request)
    {
        $content = $this->get("request")->getContent();
        if (!$content) {
            throw new BadRequestHttpException('no content');
        }
        $this->get('logger')->addInfo($content);
        $data = json_decode($content);
        if (!$data) {
            throw new BadRequestHttpException('cannot parse');
        }
        return new Response('OK');
    }

    /**
     * @Route("/success", name="mfb_reskribe_success")
     */

    public function successAction(Request $request)
    {
        $uid = $request->get('uid');
        if ($uid) {
            $this->get('mfb_account.service')->enableAccount($uid);

            $this->get('mfb_account.security.service')->login($uid, 'secured_area');
            return $this->redirect($this->generateUrl('mfb_admin_homepage'));
        }
        throw new BadRequestHttpException('no needed data set');
    }
}
