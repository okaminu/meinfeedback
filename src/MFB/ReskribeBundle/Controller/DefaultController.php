<?php

namespace MFB\ReskribeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends Controller
{
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

    public function successAction(Request $request)
    {
        $uid = $request->get('uid');
        if ($uid) {
            $em = $this->getDoctrine()->getManager();
            $account = $em->find('MFBAccountBundle:Account', $uid);
            $account->setIsEnabled(true);
            $em->persist($account);
            $em->flush();
            return $this->redirect($this->generateUrl('mfb_account_login'));
        }
        throw new BadRequestHttpException('no needed data set');
    }
}
