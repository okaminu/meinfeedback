<?php
namespace MFB\AdminBundle\Controller;

use MFB\ChannelBundle\Form\ChannelRatingSelectType;
use MFB\ChannelBundle\Form\ChannelRatingType;
use MFB\ChannelBundle\Form\ChannelServicesType;
use MFB\ServiceBundle\Entity\ServiceGroup;
use MFB\ServiceBundle\Entity\ServiceProvider;
use MFB\ServiceBundle\Form\ServiceGroupType;
use MFB\ServiceBundle\Form\ServiceProviderType;
use MFB\ServiceBundle\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class FormSetupController extends Controller
{

    public function showAction()
    {
        $accountId = $this->getCurrentUserId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        $channelCriteria = $this->get('mfb_account_channel.rating_criteria.service')
            ->createNewChannelCriteria($channel);

        return $this->render(
            'MFBAdminBundle:Default:formSetup.html.twig',
            array(
                'serviceGroupForm' => $this->getNewServiceGroupForm($accountId)->createView(),
                'serviceProviderForm' => $this->getNewServiceProviderForm($accountId)->createView(),
                'channelServicesForm' => $this->getChannelServiceForm($channel)->createView(),
                'ratingSelectionForm' => $this->getChannelRatingSelectForm($channelCriteria, $channel->getId())
                        ->createView(),
                'channelRatingCriterias' => $channel->getRatingCriteria(),
                'criteriaLimit' => $this->container->getParameter('mfb_account_channel.rating_criteria.limit'),
                'errors' => $this->getErrors()
            )
        );
    }


    private function getErrors()
    {
        $adminService = $this->get('mfb_admin.form_setup.service');
        if ($adminService->isMissingMandatorySettings($this->getCurrentUserId())) {
            return $adminService->missingMandatorySettingsErrors($this->getCurrentUserId());
        }
    }

    public function updateRatingCriteriaSelectAction(Request $request)
    {
        $accountId = $this->getCurrentUserId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        try {
            /**
             * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
             */
            $channelCriteria = $this->get('mfb_account_channel.rating_criteria.service')
                ->createNewChannelCriteria($channel);

            $form = $this->getChannelRatingSelectForm($channelCriteria, $channel->getId());
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_account_channel.rating_criteria.service')->store($channelCriteria);
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->redirect($this->generateUrl('mfb_admin_show_form_setup'));
    }

    public function saveServiceGroupAction(Request $request)
    {
        $accountId = $this->getCurrentUserId();
        try {
            /**
             * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
             */
            $serviceGroup = $this->get('mfb_account_channel.service_type.service')->createNew($accountId, null);

            $form = $this->getServiceGroupForm($serviceGroup);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_account_channel.service_type.service')->store($serviceGroup);
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->redirect($this->generateUrl('mfb_admin_show_form_setup'));
    }

    public function saveServiceProviderAction(Request $request)
    {
        $accountId = $this->getCurrentUserId();
        try {
            /**
             * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
             */
            $serviceProvider = $this->get('mfb_service_provider.service')->createNewServiceProvider($accountId);

            $form = $this->getServiceProviderForm($serviceProvider);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_service_provider.service')->store($serviceProvider);
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }
        return $this->redirect($this->generateUrl('mfb_admin_show_form_setup'));
    }


    public function updateServicesVisibilityAction(Request $request)
    {
        $accountId = $this->getCurrentUserId();
        try {
            /** @var $service \MFB\ServiceBundle\Entity\ServiceGroup */
            $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
            $channelServicesForm = $this->getChannelServiceForm($channel);

            $channelServicesForm->handleRequest($request);

            if (!$channelServicesForm->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_account_channel.service')->store($channel);
        } catch (ServiceException $ex) {
            $channelServicesForm->addError(new FormError($ex->getMessage()));
        }

        return $this->redirect($this->generateUrl('mfb_admin_show_form_setup'));
    }

    private function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    /**
     * @param $serviceGroup
     * @return \Symfony\Component\Form\Form
     */
    private function getServiceGroupForm(ServiceGroup $serviceGroup)
    {
        $form = $this->createForm(
            new ServiceGroupType(),
            $serviceGroup,
            array(
                'action' => $this->generateUrl('mfb_admin_save_service_group'),
                'method' => 'POST',
            )
        );
        $form->add('save', 'submit', array('label' => 'Send'));
        return $form;
    }

    /**
     * @param $serviceProvider
     * @return \Symfony\Component\Form\Form
     */
    private function getServiceProviderForm(ServiceProvider $serviceProvider)
    {
        $form = $this->createForm(
            $this->get('mfb_service_provider.service')->getType(),
            $serviceProvider,
            array(
                'action' => $this->generateUrl('mfb_admin_save_service_provider'),
                'method' => 'POST',
            )
        );
        $form->add('save', 'submit', array('label' => 'Send'));
        return $form;
    }

    /**
     * @param $accountId
     * @return \Symfony\Component\Form\Form
     */
    private function getNewServiceGroupForm($accountId)
    {
        $serviceGroup = $this->get('mfb_account_channel.service_type.service')->createNew($accountId, null);
        $serviceGroupForm = $this->getServiceGroupForm($serviceGroup);
        return $serviceGroupForm;
    }

    private function getNewServiceProviderForm($channelId)
    {
        $serviceProvider = $this->get('mfb_service_provider.service')->createNewServiceProvider($channelId);
        $serviceProviderForm = $this->getServiceProviderForm($serviceProvider);
        return $serviceProviderForm;
    }

    /**
     * @return mixed
     */
    private function getCurrentUserId()
    {
        return $this->getCurrentUser()->getId();
    }

    /**
     * @param $channel
     * @return \Symfony\Component\Form\Form
     */
    private function getChannelServiceForm($channel)
    {
        $channelServicesForm = $this->createForm(
            new ChannelServicesType(),
            $channel,
            array(
                'action' => $this->generateUrl('mfb_admin_update_service_visibility'),
                'method' => 'POST',
            )
        );
        $this->addServiceProviderTitles($channelServicesForm);
        return $channelServicesForm;
    }

    /**
     * @param $channelCriteria
     * @param $channelId
     * @return \Symfony\Component\Form\Form
     */
    private function getChannelRatingSelectForm($channelCriteria, $channelId)
    {
        $unusedCriterias = $this->get('mfb_account_channel.rating_criteria.service')
            ->getNotUsedRatingCriterias($channelId);

        return $this->createForm(
            new ChannelRatingSelectType($unusedCriterias),
            $channelCriteria,
            array(
                'action' => $this->generateUrl('mfb_admin_update_rating_criteria_select'),
                'method' => 'POST',
            )
        );
    }

    private function addServiceProviderTitles($form)
    {
        $prefixList = $this->get('mfb_service_provider.service')->getPrefix();
        $serviceProviderForm = $form->get('serviceProvider');
        foreach ($serviceProviderForm as $providerForm) {
            $prefixId = $providerForm->getData()->getPrefix();
            $providerForm->remove('prefix');
            $providerForm->add('prefix', 'hidden', array('label' => $prefixList[$prefixId]));
        }
    }
}
