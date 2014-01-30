<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use MFB\AdminBundle\Form\MultipleSelectType;
use MFB\ChannelBundle\ChannelException;
use MFB\ServiceBundle\Entity\ServiceDefinition;
use MFB\ServiceBundle\Form\ServiceDefinitionType;
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
        try {
            if ($form->isValid()) {
                $businessId = $form->get('choice')->getData();
                $this->createUpdateBusinessForChannel($businessId);

                return $this->createRedirect(
                    $this->getServiceSelectRoute($businessId),
                    array('businessId' => $businessId)
                );
            }
        } catch (ChannelException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return $this->showBusinessSelectForm($form);
    }

    public function selectSingleServiceTypeAction(Request $request, $businessId)
    {
        $channel = $this->getChannel();
        $serviceTypes = $this->get('mfb_service_type.service')->findByBusinessId($businessId);
        $form = $this->createSingleSelectForm($serviceTypes);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $selectedServiceId = $form->get('choice')->getData();
                $this->storeChannelServiceType($channel, $selectedServiceId);

                return $this->createDefinitionsRedirect($channel->getId());
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showSingleServiceForm($form);
    }

    public function selectMultipleServiceTypeAction(Request $request, $businessId)
    {
        $channel = $this->getChannel();
        $serviceTypes = $this->get('mfb_service_type.service')->findByBusinessId($businessId);
        $form = $this->createMultipleSelectForm($serviceTypes);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $selectedServices = $form->get('choice')->getData();

                foreach ($selectedServices as $serviceId) {
                    $this->storeChannelServiceType($channel, $serviceId);
                }
                return $this->createDefinitionsRedirect($channel->getId());
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->showMultipleServiceForm($form);
    }

    public function selectDefinitionsAction(Request $request, $channelId)
    {
        $definitionService = $this->get('mfb_service_definition.service');

        $definition = $definitionService->createNew($channelId);
        $form = $this->createForm(new ServiceDefinitionType(), $definition);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $definitionService->store($definition);
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return $this->showDefinitionForm($form, $definitionService->findByChannelId($channelId));
    }

    public function removeDefinitionsAction($definitionId)
    {
        $channel = $this->getChannel();

        $definitionService = $this->get('mfb_service_definition.service');
        $definition = $definitionService->findByChannelIdAndId($channel->getId(), $definitionId);
        $definitionService->remove($definition);

        return $this->createDefinitionsRedirect($channel->getId());
    }

    private function createRedirect($path, $options)
    {
        return $this->redirect($this->generateUrl($path, $options));
    }

    private function getLoggedInUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    private function getChannel()
    {
        $accountId = $this->getLoggedInUser()->getId();
        return $this->get('mfb_account_channel.service')->findByAccountId($accountId);
    }

    public function createUpdateBusinessForChannel($businessId)
    {
        $channelService = $this->get('mfb_account_channel.service');
        $channel = $this->getChannel();

        if ($channel == null) {
            $channel = $channelService->createNew($this->getLoggedInUser()->getId());
        }

        if ($channel->getBusiness() != null) {
            throw new ChannelException('Business is already selected');
        }
        $business = $this->get('mfb_service_business.service')->findById($businessId);
        $channel->setBusiness($business);

        $channelService->store($channel);
    }


    private function storeChannelServiceType($channel, $selectedServiceId)
    {
        $this->get('mfb_account_channel.service_type.service')
            ->createStoreNew($channel->getId(), $selectedServiceId);
    }

    private function createSingleSelectForm($list)
    {
        $choices = array();
        foreach ($list as $entity) {
            $choices[$entity->getId()] = $entity->getName();
        }
        return $this->createForm(new SingleSelectType($choices));
    }

    private function createMultipleSelectForm($list)
    {
        $choices = array();
        foreach ($list as $entity) {
            $choices[$entity->getId()] = $entity->getName();
        }
        return $this->createForm(new MultipleSelectType($choices));
    }
    private function showBusinessSelectForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:businessSelect.html.twig",
            array('form' => $form->createView())
        );
    }

    private function showSingleServiceForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:serviceSelectSingle.html.twig",
            array('form' => $form->createView())
        );
    }

    private function showMultipleServiceForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:serviceSelectMultiple.html.twig",
            array('form' => $form->createView())
        );
    }

    private function showDefinitionForm($form, $definitionList)
    {
        return $this->render(
            'MFBAdminBundle:SetupWizard:definition.html.twig',
            array(
                'form' => $form->createView(),
                'definitionList' => $definitionList
            )
        );
    }


    private function createDefinitionsRedirect($channelId)
    {
        return $this->createRedirect(
            'mfb_admin_setup_select_definitions',
            array('channelId' => $channelId)
        );
    }

    private function getServiceSelectRoute($businessId)
    {
        $path = 'mfb_admin_setup_select_single_service';

        $businessEntity = $this->get('mfb_service_business.service')->findById($businessId);
        if ($businessEntity->getIsMultipleServices() == 1) {
            $path = 'mfb_admin_setup_select_multiple_service';
            return $path;
        }
        return $path;
    }
}
