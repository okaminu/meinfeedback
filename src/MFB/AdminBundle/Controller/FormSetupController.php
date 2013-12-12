<?php
namespace MFB\AdminBundle\Controller;

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
        $accountId = $this->getCurrentUser()->getId();
        /**
         * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
         */
        $serviceGroup = $this->get('mfb_service.service')->createNewServiceGroup($accountId);
        $serviceProvider = $this->get('mfb_service.service')->createNewServiceProvider($accountId);
        $serviceGroupForm = $this->getServiceGroupForm($serviceGroup);
        $serviceProviderForm = $this->getServiceProviderForm($serviceProvider);

        return $this->render(
            'MFBAdminBundle:Default:formSetup.html.twig',
            array(
                'serviceGroupForm' => $serviceGroupForm->createView(),
                'serviceProviderForm' => $serviceProviderForm->createView()

            )
        );
    }

    public function saveServiceGroupAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        try {
            /**
             * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
             */
            $serviceGroup = $this->get('mfb_service.service')->createNewServiceGroup($accountId);

            $form = $this->getServiceGroupForm($serviceGroup);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_service.service')->store($serviceGroup);
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->redirect($this->generateUrl('mfb_admin_show_form_setup'));
    }

    public function saveServiceProviderAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        try {
            /**
             * @var $service \MFB\ServiceBundle\Entity\ServiceGroup
             */
            $serviceProvider = $this->get('mfb_service.service')->createNewServiceProvider($accountId);

            $form = $this->getServiceProviderForm($serviceProvider);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_service.service')->store($serviceProvider);
        } catch (ServiceException $ex) {
            $form->addError(new FormError($ex->getMessage()));
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
            new ServiceProviderType(),
            $serviceProvider,
            array(
                'action' => $this->generateUrl('mfb_admin_save_service_provider'),
                'method' => 'POST',
            )
        );
        $form->add('save', 'submit', array('label' => 'Send'));
        return $form;
    }
}
