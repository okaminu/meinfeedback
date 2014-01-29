<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use MFB\ServiceBundle\ServiceException;

class SetupWizardController extends Controller
{
    public function selectBusinessAction(Request $request)
    {
        $businessList = $this->get('mfb_service_business.service')->findAll();
        $form = $this->createSingleSelectForm($businessList);

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->createRedirect(
                'mfb_admin_setup_select_service_type',
                array('businessId' => $form->get('choice')->getData())
            );
        }
        return $this->showBusinessSelectForm($form);
    }

    public function selectServiceTypeAction(Request $request, $businessId)
    {
        $channel = $this->getChannel();

        $serviceTypes = $this->get('mfb_service_type.service')->findByBusinessId($businessId);
        $form = $this->createSingleSelectForm($serviceTypes);

        $form->handleRequest($request);

        try {
            if ($form->isValid()) {
                $selectedServiceId = $form->get('choice')->getData();
                $this->get('mfb_account_channel.service_type.service')
                    ->createStoreNew($channel->getId(), $selectedServiceId);

                return $this->createRedirect(
                    'mfb_admin_setup_select_definitions',
                    array('channelId' => $channel->getId())
                );
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showServiceSelectForm($form);
    }

    public function selectDefinitionsAction(Request $request, $channelId)
    {
        $temp = $channelId;
    }

    private function createSingleSelectForm($list)
    {
        $choices = array();
        foreach ($list as $entity) {
            $choices[$entity->getId()] = $entity->getName();
        }
        return $this->createForm(new SingleSelectType($choices));
    }

    private function createRedirect($path, $options)
    {
        return $this->redirect($this->generateUrl($path, $options));
    }

    private function showBusinessSelectForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:businessSelect.html.twig",
            array('form' => $form->createView())
        );
    }

    private function showServiceSelectForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:serviceSelect.html.twig",
            array('form' => $form->createView())
        );
    }

    private function getLoggedInUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    private function getChannel()
    {
        $accountId = $this->getLoggedInUser()->getId();

        $channelService = $this->get('mfb_account_channel.service');
        $channel = $channelService->findByAccountId($accountId);

        if (!$channel) {
            $channelService->createStoreNew($accountId);
            $channel = $channelService->findByAccountId($accountId);
        }
        return $channel;
    }


}
