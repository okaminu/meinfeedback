<?php
namespace MFB\AdminBundle\Controller;

use MFB\ChannelBundle\Form\ChannelServicesType as ChannelServiceFormType;
use MFB\ServiceBundle\Entity\ServiceProvider;
use MFB\ServiceBundle\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class FormSetupController extends Controller
{

    public function showAction()
    {
        $channel = $this->getChannel();

        return $this->render(
            'MFBAdminBundle:Default:formSetup.html.twig',
            array(
                'serviceProviderForm' => $this->getNewServiceProviderForm($channel->getId())->createView(),
                'channelServicesForm' => $this->getChannelServiceForm($channel)->createView(),
                'channelRatingCriterias' => $channel->getRatingCriteria(),
                'criteriaLimit' => $this->container->getParameter('mfb_account_channel.rating_criteria.limit')
            )
        );
    }

    public function saveServiceProviderAction(Request $request)
    {
        $channel = $this->getChannel();
        try {
            $serviceProvider = $this->get('mfb_service_provider.service')->createNewServiceProvider($channel->getId());

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
        $channel = $this->getChannel();
        try {
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

    private function getNewServiceProviderForm($channelId)
    {
        $serviceProvider = $this->get('mfb_service_provider.service')->createNewServiceProvider($channelId);
        $serviceProviderForm = $this->getServiceProviderForm($serviceProvider);
        return $serviceProviderForm;
    }

    private function getCurrentUserId()
    {
        return $this->getCurrentUser()->getId();
    }

    private function getChannelServiceForm($channel)
    {
        $channelServicesForm = $this->createForm(
            new ChannelServiceFormType(),
            $channel,
            array(
                'action' => $this->generateUrl('mfb_admin_update_service_visibility'),
                'method' => 'POST',
            )
        );
        $this->addServiceProviderTitles($channelServicesForm);
        return $channelServicesForm;
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

    private function getChannel()
    {
        $accountId = $this->getCurrentUserId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        return $channel;
    }
}
