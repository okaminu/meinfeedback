<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Form\EmailTemplateType;
use MFB\EmailBundle\Form\ThankYouTemplateType;
use MFB\EmailBundle\Form\VariableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\EmailBundle\Service\Template;

class EmailTemplatesController extends Controller
{


    public function selectVariablesAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var EmailTemplate $emailTemplate  */
        $emailTemplate =$this->get('mfb_email.template')->getEmailTemplate($this->getUserId());

        $typesToFilter = array('firstname', 'service_name', 'service_date', 'service_id', 'customer_id','reference_id');

        $filteredVariables = $emailTemplate->getVariables()->filter(
            function($entry) use ($typesToFilter){
                return in_array($entry->getType(), $typesToFilter);
            });

        $emailTemplate->setVariables($filteredVariables);

        $form = $this->createForm(
            new VariableType(),
            $emailTemplate,
            array(
                'action' => $this->generateUrl('mfb_admin_select_variables'),
                'method' => 'PUT'
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($emailTemplate);
            $entityManager->flush();

            $this->removesUnwantedVariables($entityManager);
            $this->addMandatoryVariables($entityManager);

            return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
        }
        return $this->render(
            'MFBAdminBundle:EmailTemplates:selectVariables.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accountId = $this->getUserId();

        /**
         * @var $emailTemplate \MFB\EmailBundle\Entity\EmailTemplate
         */
        $emailTemplate = $this->get('mfb_email.template')->getEmailTemplate($accountId);
        $editForm = $this->createEditForm($emailTemplate);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setThankYouCode($this->plain2html($emailTemplate->getThankYouCode()));
            $emailTemplate->setTemplateTypeId(Template::EMAIL_TEMPLATE_TYPE);

            $notUsedVariables = $this->getMandatoryAndUnusedVariables($emailTemplate);

            if(count($notUsedVariables) > 0){
                $showErrors = 'The following variables were not used: '. implode(' , ', $notUsedVariables);
                return $this->showEmailTemplate($accountId, $showErrors);
            }

            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setThankYouCode($this->plain2html($emailTemplate->getThankYouCode()));

            $emailTemplate->setTemplateTypeId(Template::EMAIL_TEMPLATE_TYPE);
            $em->persist($emailTemplate);
            $em->flush();
        }

        return $this->showEmailTemplate($accountId);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function thankYouEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accountId = $this->getUserId();

        /**
         * @var $emailTemplate \MFB\EmailBundle\Entity\EmailTemplate
         */
        $thankYouTemplate = $this->get('mfb_email.template')->getThankYouTemplate($accountId);

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);

        $thankYouForm->handleRequest($request);
        if ($thankYouForm->isValid()) {
            $thankYouTemplate->setTemplateCode($this->plain2html($thankYouTemplate->getTemplateCode()));
            $thankYouTemplate->setTemplateTypeId(Template::THANKYOU_TEMPLATE_TYPE);
            $em->persist($thankYouTemplate);
            $em->flush();

            return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
        }

        return $this->showEmailTemplate($accountId);
    }

    /**
     * @param $accountId
     * @param $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmailTemplate($accountId, $errors = null)
    {
        /** @var EmailTemplate $emailTemplate  */
        $emailTemplate = $this->get('mfb_email.template')->getEmailTemplate($accountId);
        $thankYouTemplate = $this->get('mfb_email.template')->getThankYouTemplate($accountId);

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);
        $editForm = $this->createEditForm($emailTemplate);

        $variables = $this->get('mfb_email.variables')->getVariables($emailTemplate);
        $editForm->get('templateCode')->setData($this->html2plain($emailTemplate->getTemplateCode()));
        $editForm->get('thankYouCode')->setData($this->html2plain($emailTemplate->getThankYouCode()));

        $thankYouForm->get('templateCode')->setData($this->html2plain($thankYouTemplate->getTemplateCode()));
        $thankyou_variables = $this->get('mfb_email.variables')->getVariables($thankYouTemplate);

        return $this->render(
            'MFBAdminBundle:EmailTemplates:edit.html.twig',
            array(
                'errors' => $errors,
                'variables' => $variables,
                'entity' => $emailTemplate,
                'form' => $editForm->createView(),
                'thankyou_entity' => $thankYouTemplate,
                'form_thankyou' => $thankYouForm->createView(),
                'thankyou_variables' => $thankyou_variables,
            )
        );
    }

    public function listPossibleVariablesAction($emailTemplateId)
    {
        $em = $this->getDoctrine()->getManager();
        $emailTemplate = $em->find('MFBEmailBundle:EmailTemplate', $emailTemplateId);

        $variables = $this->get('mfb_email.variables')->getPossibleVariables($emailTemplate);
        return $this->render(
            'MFBAdminBundle:EmailTemplates:possibleVariablesList.html.twig',
            array(
                'emailTemplateId' => $emailTemplateId,
                'variables' => $variables
            )
        );
    }

    /**
     * Creates a form to edit a EmailTemplate entity.
     *
     * @param EmailTemplate $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EmailTemplate $entity)
    {
        $form = $this->createForm(
            new EmailTemplateType(),
            $entity,
            array(
                'action' => $this->generateUrl('mfb_admin_edit_email_template', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

    /**
     * Creates a form to edit a EmailTemplate entity.
     *
     * @param EmailTemplate $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createThankYouForm(EmailTemplate $entity)
    {
        $form = $this->createForm(
            new ThankYouTemplateType(),
            $entity,
            array(
                'action' => $this->generateUrl('mfb_admin_edit_thankyou_template', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

    private function html2plain($html)
    {
        $converter = new \MFB\HtmlToText\Converter();
        return $converter->html2text($html);
    }

    private function plain2html($text)
    {
        $paragraphs = preg_split('#\s*\n\s*\n\s*#', $text);
        $text = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = str_replace("\n", "<br/>", $paragraph);
            $text .= "<p>{$paragraph}</p>\n";
        }
        return $text;
    }


    /**
     * @return mixed
     */
    public function getUserId()
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();
        return $accountId;
    }

    /**
     * @return array TODO: this needs to be moved to email service configuration
     */
    private function getAllVariables()
    {
        $variables = array(
            'link' => '#LINK#',
            'lastname' => '#LASTNAME#',
            'salutation' => '#SAL#',
            'email' => '#EMAIL#',
            'homepage' => '#HOMEPAGE#',
            'firstname' => '#FIRSTNAME#',
            'service_name' => '#SERVICE_NAME#',
            'service_date' => '#SERVICE_DATE#',
            'reference_id' => '#REFERENCE_ID#',
            'customer_id' => '#CUSTOMER_ID#',
            'service_id' => '#SERVICE_ID#'
        );
        return $variables;
    }

    /**
     * Gets a list of unused variables TODO: this needs to be moved to email service
     * @param $emailTemplate
     * @return array
     */
    private function getMandatoryAndUnusedVariables($emailTemplate)
    {
        $templateCode = $emailTemplate->getTemplateCode();
        $thankYouCode = $emailTemplate->getThankYouCode();
        $fullMailCode = $templateCode . $thankYouCode;

        $activeValues = $this->getVariables($emailTemplate, true);

        $variables = $this->getAllVariables();

        $notUsedVariables = array();
        foreach ($activeValues as $value) {
            $typeCode = $variables[$value->getType()];
            $count = substr_count($fullMailCode, $typeCode);
            if ($count == 0) {
                $notUsedVariables[] = $typeCode;
            }
        }
        return $notUsedVariables;
    }

    /**
     * @param $emailTemplate TODO: this needs to be moved to email service
     * @param $active
     * @return mixed
     */
    private function getVariables($emailTemplate, $active)
    {
        $selectedVariables = $emailTemplate->getVariables()->filter(
            function ($entity) use($active){
                return $entity->getIsActive() == $active;
            }
        );

        $values = $selectedVariables->getValues();
        return $values;
    }

    /**
     * @param $entityManager TODO: this needs to be moved to email service
     */
    private function addMandatoryVariables($entityManager)
    {
        /** @var EmailTemplate $emailTemplate  */
        $emailTemplate =$this->get('mfb_email.template')->getEmailTemplate($this->getUserId());
        $missingVariables = $this->getMandatoryAndUnusedVariables($emailTemplate);

        $templateWithMissingVars = $emailTemplate->getTemplateCode() .implode('<br>', $missingVariables);
        $emailTemplate->setTemplateCode($this->plain2html($templateWithMissingVars));
        $entityManager->persist($emailTemplate);
        $entityManager->flush();
    }

    /**
     * @param $entityManager TODO: this needs to be moved to email service
     */
    private function removesUnwantedVariables($entityManager)
    {
        /** @var EmailTemplate $emailTemplate  */
        $emailTemplate =$this->get('mfb_email.template')->getEmailTemplate($this->getUserId());
        $inactiveVariables = $this->getVariables($emailTemplate, false);
        $allValues = $this->getAllVariables();
        $unwantedValueCodes = array();

        foreach ($inactiveVariables as $variable) {
            $unwantedValueCodes[] = $allValues[$variable->getType()];
        }

        $templateWithoutVars = str_replace($unwantedValueCodes, '', $emailTemplate->getTemplateCode());
        $emailTemplate->setTemplateCode($this->plain2html($templateWithoutVars));
        $entityManager->persist($emailTemplate);
        $entityManager->flush();
    }
}
