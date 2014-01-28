<?php

namespace MFB\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SetupWizardController extends Controller
{
    public function selectBusinessAction(Request $request)
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
            array('multiple' => false, 'expanded' => true, 'mapped' => false, 'choices' => $choices)
        );
        $builder->add('submit', 'submit', array('label' => 'Continue'));
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $value = $form->get('businessType')->getData();
            return $this->redirect(
                $this->generateUrl(
                    'mfb_admin_setup_select_service_type',
                    array('businessId' => $value)
                )
            );
        }

        return $this->render(
            "MFBAdminBundle:SetupWizard:selectBusiness.html.twig",
            array(
                'businessList' => $businessList,
                'form' => $form->createView()
            )
        );
    }

    public function selectServiceTypeAction($businessId)
    {
        $temp = $businessId;
        exit;
    }


}
