<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SetupWizardController extends Controller
{
    public function selectBusinessAction(Request $request)
    {
        $form = $this->createBusinessForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->createRedirect(
                'mfb_admin_setup_select_service_type',
                array('businessId' => $form->get('businessType')->getData())
            );
        }
        return $this->showSelectBusinessForm($form);
    }

    public function selectServiceTypeAction($businessId)
    {
        $temp = $businessId;
        exit;
    }

    private function createBusinessForm()
    {
        $businessList = $this->get('mfb_service_business.service')->findAll();
        $choices = array();
        foreach ($businessList as $business) {
            $choices[$business->getId()] = $business->getName();
        }

        $builder = $this->createFormBuilder();
        $builder->add(
            'businessType',
            'choice',
            array(
                'multiple' => false,
                'expanded' => true,
                'mapped' => false,
                'choices' => $choices
            )
        );

        $builder->add('submit', 'submit', array('label' => 'Continue'));
        return $builder->getForm();
    }

    private function createRedirect($path, $options)
    {
        return $this->redirect($this->generateUrl($path, $options));
    }

    private function showSelectBusinessForm($form)
    {
        return $this->render(
            "MFBAdminBundle:SetupWizard:selectBusiness.html.twig",
            array('form' => $form->createView())
        );
    }
}
