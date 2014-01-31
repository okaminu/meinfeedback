<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use MFB\AdminBundle\Form\MultipleSelectType;
use MFB\ChannelBundle\ChannelException;
use MFB\ServiceBundle\Form\ServiceDefinitionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use MFB\ServiceBundle\ServiceException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SetupWizardController extends Controller
{
    /**
     * @Route("/setup_select_business", name="mfb_admin_setup_select_business")
     * @Template
     */

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
        return array('form' => $form->createView());
    }

    /**
     * @Route("/setup_select_single_service/{businessId}", name="mfb_admin_setup_select_single_service",
     * requirements={"businessId" = "\d+"})
     * @Template
     */

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

                return $this->createRedirect('mfb_admin_setup_insert_definitions');
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/setup_select_multiple_service/{businessId}", name="mfb_admin_setup_select_multiple_service",
     * requirements={"businessId" = "\d+"})
     * @Template
     */

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
                return $this->createRedirect('mfb_admin_setup_insert_definitions');
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/setup_insert_definitions", name="mfb_admin_setup_insert_definitions")
     * @Template
     */

    public function insertDefinitionsAction(Request $request)
    {
        $channelId = $this->getChannel()->getId();
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
        return array('form' => $form->createView(),'definitionList' => $definitionService->findByChannelId($channelId));
    }

    /**
     * @Route("/setup_remove_definitions/{definitionId}", name="mfb_admin_setup_remove_definitions",
     * requirements={"definitionId" = "\d+"})
     * @Method({"POST"})
     * @Template
     */

    public function removeDefinitionsAction($definitionId)
    {
        $channel = $this->getChannel();

        $definitionService = $this->get('mfb_service_definition.service');
        $definition = $definitionService->findByChannelIdAndId($channel->getId(), $definitionId);
        $definitionService->remove($definition);

        return $this->createRedirect('mfb_admin_setup_insert_definitions');
    }

    /**
     * @Route("/setup_select_criterias", name="mfb_admin_setup_select_criterias")
     * @Template
     */

    public function selectCriteriasAction()
    {
        return array();
    }


    private function createRedirect($path, $options = array())
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
