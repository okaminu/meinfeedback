<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HelperController extends Controller
{
    public function getCompanyNameAction()
    {
        $entity = $this->get('mfb_account.service')->findByAccountId($this->getUserId());
        return $this->showText($entity->getName());
    }

    public function hasCriteriasAction($print)
    {
        $hasSelected = $this->get('mfb_account_channel.rating_criteria.service')
            ->hasSelectedRatingCriterias($this->getUserId());
        if ($hasSelected) {
            return $this->showText($print);
        }
        return $this->showText('');
    }

    private function showText($text)
    {
        return $this->render(
            'MFBAdminBundle:Helper:helper.html.twig',
            array('helperData' => $text)
        );
    }

    private function getUserId()
    {
        $token = $this->get('security.context')->getToken();
        return $token->getUser()->getId();
    }

}
