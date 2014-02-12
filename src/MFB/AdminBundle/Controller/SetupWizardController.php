<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use MFB\AdminBundle\Form\MultipleSelectType;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\ChannelBundle\Form\ChannelRatingSelectType;
use MFB\ChannelBundle\Form\ChannelServiceDefinitionType;
use MFB\RatingBundle\RatingException;
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
        $businessList = $this->get('mfb_service_business.service')->getDefaultBusinesses();

        $form = $this->createSingleSelectForm($businessList);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {

                $businessId = $this->getBusinessIdFromSubmit($form);
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
        $serviceTypes = $this->get('mfb_service_type.service')->getDefaultByBusinessId($businessId);
        $form = $this->createSingleSelectForm($serviceTypes);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $selectedServiceId = $this->getServiceIdFromSubmit($form, $businessId);
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
        $serviceTypes = $this->get('mfb_service_type.service')->getDefaultByBusinessId($businessId);
        $form = $this->createMultipleSelectForm($serviceTypes);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $selectedServices = $this->getServiceIdlistFromSubmit($form, $businessId);
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
        $definitionService = $this->get('mfb_channel_definition.service');

        $definition = $definitionService->createNewCustom($channelId);
        $form = $this->createForm(new ChannelServiceDefinitionType(), $definition);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $definitionService->store($definition);
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return array('form' => $form->createView(),'definitionList' => array());
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
     */
    public function selectCriteriasAction(Request $request)
    {
        $channel = $this->getChannel();
        $channelRatingService = $this->get('mfb_account_channel.rating_criteria.service');

        $channelCriteria = $channelRatingService->createNew($channel);
        $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());
        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $this->storeChannelRatingCriteria($channelCriteria, $form->get('customRatingName')->getData());
                $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());
            }
        } catch (RatingException $ex) {
            $form->addError(new FormError($this->get('translator')->trans('Please insert rating criteria')));
        }
        return $this->showCriteriaSelectForm($form);
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
                'action' => $this->generateUrl('mfb_admin_setup_select_criterias'),
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

    private function getBusinessIdFromSubmit($form)
    {
        $choice = $form->get('choice')->getData();

        if ($choice == 'customInputOption') {
            $businessEntity = $this->get('mfb_service_business.service')->createCustom(
                $form->get('customInputText')->getData()
            );
            $this->get('mfb_service_business.service')->store($businessEntity);
            $choice = $businessEntity->getId();
        }
        return $choice;
    }

    private function getServiceIdFromSubmit($form, $businessId)
    {
        $choice = $form->get('choice')->getData();

        if ($choice == 'customInputOption') {
            $serviceEntity = $this->get('mfb_service_type.service')->createCustom(
                $businessId,
                $form->get('customInputText')->getData()
            );
            $this->get('mfb_service_type.service')->store($serviceEntity);
            $choice = $serviceEntity->getId();
        }
        return $choice;
    }

    private function getServiceIdListFromSubmit($form, $businessId)
    {
        $choices = $form->get('choice')->getData();

        if ($customChoice = array_search('customInputOption', $choices)) {
            $serviceEntity = $this->get('mfb_service_type.service')->createCustom(
                $businessId,
                $form->get('customInputText')->getData()
            );
            $this->get('mfb_service_type.service')->store($serviceEntity);
            $choices = array_merge($this->removeCustomChoice($choices), array($serviceEntity->getId()));
        }
        return $choices;
    }

    private function removeCustomChoice($choices)
    {
        $customChoice = array_search('customInputOption', $choices);
        unset($choices[$customChoice]);
        return $choices;
    }

    private function showCriteriaSelectForm($form)
    {
        $channel = $this->getChannel();
        return $this->render(
            'MFBAdminBundle:SetupWizard:selectCriterias.html.twig',
            array(
                'ratingSelectionForm' => $form->createView(),
                'channelRatingCriterias' => $channel->getRatingCriteria(),
                'criteriaLimit' => $this->container->getParameter('mfb_account_channel.rating_criteria.limit'),
                'neededCriteriaCount' => $this->get('mfb_account_channel.rating_criteria.service')
                        ->missingCount($channel->getId())
            )
        );
    }

    private function storeChannelRatingCriteria($channelCriteria, $customRatingName)
    {
        if ($channelCriteria->getRatingCriteria() == null) {
            $rating = $this->get('mfb_rating.service')->createCustom($customRatingName);
            $this->get('mfb_rating.service')->store($rating);
            $channelCriteria->setRatingCriteria($rating);
        }

        $this->get('mfb_account_channel.rating_criteria.service')->store($channelCriteria);
    }


}
