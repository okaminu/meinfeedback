<?php

namespace MFB\AdminBundle\Controller;

use MFB\AdminBundle\Form\SingleSelectType;
use MFB\AdminBundle\Form\MultipleSelectType;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\ChannelBundle\Form\ChannelRatingSelectType;
use MFB\ChannelBundle\Form\ChannelServiceDefinitionType;
use MFB\RatingBundle\RatingException;
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
     * @Route("/setup_wizard", name="mfb_admin_setup_wizard")
     */

    public function setupWizardAction(Request $request)
    {
        return $this->get('mfb_setup_wizard.service')->getNextStep();
    }

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

                return $this->getNextStep("mfb_admin_setup_select_business");
            }
        } catch (ChannelException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/setup_select_service", name="mfb_admin_setup_select_service")
     */

    public function selectServiceTypeAction(Request $request)
    {
        $channel = $this->getChannel();
        $businessId = $channel->getBusiness()->getId();

        $serviceTypes = $this->get('mfb_service_type.service')->getDefaultByBusinessId($businessId);
        $form = $this->getServiceTypeFrom($serviceTypes);

        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $this->storeSelectedServiceTypes($form, $businessId, $channel);
                return $this->getNextStep('mfb_admin_setup_select_service');
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return $this->getSelectServiceTypeFrom($form);
    }

    /**
     * @Route("/setup_insert_definitions", name="mfb_admin_setup_insert_definitions")
     * @Template
     */

    public function insertDefinitionsAction(Request $request)
    {
        $channelId = $this->getChannel()->getId();

        $channelDefinition = $this->get('mfb_channel_definition.service')->createNewCustom($channelId);

        $form = $this->getChannelDefinitionForm($channelId, $channelDefinition);
        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $this->storeChannelDefinitionFromSubmit($channelDefinition, $form->get('customDefName')->getData());
                $form = $this->getChannelDefinitionForm($channelId, $channelDefinition);
            }
        } catch (ServiceException $ex) {
            $form->addError(new FormError($this->get('translator')->trans('Please insert service definition')));
        }
        return array(
            'form' => $form->createView(),
            'channelDefinitionList' => $this->get('mfb_channel_definition.service')->findByChannelId($channelId)
        );
    }

    /**
     * @Route("/setup_remove_definitions/{definitionId}", name="mfb_admin_setup_remove_definitions",
     * requirements={"definitionId" = "\d+"})
     */

    public function removeDefinitionsAction($definitionId)
    {
        $channel = $this->getChannel();

        $definitionService = $this->get('mfb_channel_definition.service');
        $definition = $definitionService->findByChannelAndDefinition($channel->getId(), $definitionId);
        $definitionService->remove($definition);

        return $this->redirect($this->generateUrl('mfb_admin_setup_insert_definitions'));
    }

    /**
     * @Route("/setup_select_criterias", name="mfb_admin_setup_select_criterias")
     */
    public function selectCriteriasAction(Request $request)
    {
        $channel = $this->getChannel();

        $channelCriteria = $this->get('mfb_account_channel.rating_criteria.service')->createNew($channel);
        $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());
        $form->handleRequest($request);
        try {
            if ($form->isValid()) {
                $this->storeChannelRatingCriteria($channelCriteria, $form->get('customRatingName')->getData());
                if ($this->get('mfb_account_channel.rating_criteria.service')->missingCount($channel->getId()) == 0) {
                    return $this->getNextStep('mfb_admin_setup_select_criterias');
                }
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
        return array('form' => $form->createView(), 'addedTeamMemberName' => $addedMemberName);
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
                return $this->getNextStep('mfb_admin_setup_account_settings');
            }
        } catch (ChannelException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return array('form' => $form->createView());
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
        $form->add('save', 'submit', array('label' => 'Add'));
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

    public function storeSelectedServiceTypes($form, $businessId, $channel)
    {
        if (is_array($form->get('choice')->getData())) {
            $selectedServices = $this->getServiceIdlistFromSubmit($form, $businessId);
        } else {
            $selectedServices = array($this->getServiceIdFromSubmit($form, $businessId));
        }
        foreach ($selectedServices as $serviceId) {
            $this->storeChannelServiceType($channel, $serviceId);
        }
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
                'form' => $form->createView(),
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


    public function storeChannelDefinitionFromSubmit($channelDefinition, $customName)
    {
        if ($channelDefinition->getServiceDefinition() == null) {
            $customDefinition = $this->get('mfb_service_definition.service')->createCustom($customName);
            $this->get('mfb_service_definition.service')->store($customDefinition);
            $channelDefinition->setServiceDefinition($customDefinition);
        }

        $this->get('mfb_channel_definition.service')->store($channelDefinition);
    }

    public function getChannelDefinitionForm($channelId, $channelDefinition)
    {
        $defChoices = $this->getUnusedChannelDefinitions($channelId);

        $form = $this->createForm(new ChannelServiceDefinitionType($defChoices), $channelDefinition);
        return $form;
    }

    private function getUnusedChannelDefinitions($channelId)
    {
        $selectedDefinitionsIds = $this->get('mfb_channel_definition.service')
            ->findDefinitionIdsByChannelId($channelId);
        $serviceTypes = $this->get('mfb_account_channel.service_type.service')->findByChannelId($channelId);

        $definitions = array();
        foreach ($serviceTypes as $type) {
            $additionalDefinitions = $this->filterUnselectedDefinition(
                $type->getServiceType()->getDefinitions(),
                $selectedDefinitionsIds
            );
            $definitions = array_merge($definitions, $additionalDefinitions);
        }
        return $definitions;
    }

    private function filterUnselectedDefinition($serviceDefs, $selectedDefinitionsIds)
    {
        $definitions = array();
        foreach ($serviceDefs as $serviceDef) {
            if (!in_array($serviceDef->getId(), $selectedDefinitionsIds)) {
                $definitions[$serviceDef->getName()] = $serviceDef;
            }
        }
        return $definitions;
    }

    private function isCurrentChannelMultipleServices()
    {
        $channel = $this->getChannel();
        $businessEntity = $this->get('mfb_service_business.service')->findById($channel->getBusiness()->getId());
        return $businessEntity->getIsMultipleServices();
    }

    private function getServiceTypeFrom($serviceTypes)
    {
        $form = $this->createSingleSelectForm($serviceTypes);
        if ($this->isCurrentChannelMultipleServices()) {
            $form = $this->createMultipleSelectForm($serviceTypes);
        }
        return $form;
    }

    private function getSelectServiceTypeFrom($form)
    {
        $template = 'MFBAdminBundle:SetupWizard:selectSingleServiceType.html.twig';
        if ($this->isCurrentChannelMultipleServices()) {
            $template = 'MFBAdminBundle:SetupWizard:selectMultipleServiceType.html.twig';
        }
        return $this->render($template, array('form' => $form->createView()));
    }

    private function getNextStep($currentStep)
    {
        return $this->get('mfb_setup_wizard.service')->getNextStep($currentStep);
    }
}
