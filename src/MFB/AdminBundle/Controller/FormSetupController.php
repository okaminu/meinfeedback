<?php
namespace MFB\AdminBundle\Controller;

use MFB\ServiceBundle\Entity\ServiceGroup;
use MFB\ServiceBundle\Form\ServiceGroupType;
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
        $form = $this->getServiceGroupForm($serviceGroup);
        return $this->render(
            'MFBAdminBundle:Default:formSetup.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function saveAction(Request $request)
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
                'action' => $this->generateUrl('mfb_admin_save_form_setup'),
                'method' => 'POST',
            )
        );
        $form->add('save', 'submit', array('label' => 'Send'));
        return $form;
    }
}
