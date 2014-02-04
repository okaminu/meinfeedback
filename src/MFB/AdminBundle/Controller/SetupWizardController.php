<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use MFB\AdminBundle\Form\MultipleSelectType;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\ChannelBundle\Form\ChannelRatingSelectType;
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
     * @Route("/setup_show_criterias", name="mfb_admin_setup_show_criterias")
     * @Template
     */

    public function showCriteriasFormAction()
    {
        $channel = $this->getChannel();
        $channelRatingService = $this->get('mfb_account_channel.rating_criteria.service');

        $channelCriteria = $channelRatingService->createNewChannelCriteria($channel);
        $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());

        return
            array(
                'ratingSelectionForm' => $form->createView(),
                'channelRatingCriterias' => $channel->getRatingCriteria(),
                'criteriaLimit' => $this->container->getParameter('mfb_account_channel.rating_criteria.limit')
        );
    }

    /**
     * @Route("/setup_save_criterias", name="mfb_admin_setup_save_criterias")
     */
    public function saveCriteriasAction(Request $request)
    {
        $channel = $this->getChannel();
        $channelRatingService = $this->get('mfb_account_channel.rating_criteria.service');

        $channelCriteria = $channelRatingService->createNewChannelCriteria($channel);
        $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channelRatingService->store($channelCriteria);
        }

        return $this->createRedirect('mfb_admin_setup_show_criterias');
    }


    /**
     * @Route("/setup_insert_team_member", name="mfb_admin_setup_insert_service_provider")
     * @Template
     */
    public function insertServiceProviderAction(Request $request)
    {
        $providerService = $this->get('mfb_service_provider.service');
        $channelId = $this->getChannel()->getId();
        $addedMemberName = null;

        $teamMember = $providerService->createNewServiceProvider($channelId);
        $form = $this->getNewServiceProviderForm($teamMember);
        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $this->get('mfb_service_provider.service')->store($teamMember);
                $addedMemberName = $teamMember->getFirstname() .' '. $teamMember->getLastname();
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return array('serviceProviderForm' => $form->createView(), 'addedTeamMemberName' => $addedMemberName);
    }

    /**
     * @Route("/setup_account_settings", name="mfb_admin_setup_account_settings")
     * @Template
     */
    public function accountSettingsAction(Request $request)
    {
        $channel = $this->getChannel();
        $form = $this->createForm(new AccountChannelType(), $channel);
        $form->handleRequest($request);

        try {
            if ($form->isValid()) {
                $this->get('mfb_account_channel.service')->store($channel);
                return $this->createRedirect('mfb_admin_homepage');
            }
        } catch (ChannelException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return array('form' => $form->createView());
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

    private function getChannelRatingSelectForm($channelCriteria, $channelId)
    {
        $unusedCriterias = $this->get('mfb_account_channel.rating_criteria.service')
            ->getNotUsedCriteriasForService($channelId);

        return $this->createForm(
            new ChannelRatingSelectType($unusedCriterias),
            $channelCriteria,
            array(
                'action' => $this->generateUrl('mfb_admin_setup_save_criterias'),
                'method' => 'POST'
            )
        );
    }

    private function getNewServiceProviderForm($serviceProvider)
    {
        $form =  $this->createForm(
            $this->get('mfb_service_provider.service')->getType(),
            $serviceProvider
        );
        $form->add('save', 'submit', array('label' => 'Send'));
        return $form;
    }
}
