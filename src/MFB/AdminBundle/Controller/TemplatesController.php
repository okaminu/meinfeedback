<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Form\EmailTemplateType;
use MFB\EmailBundle\Form\ThankYouTemplateType;
use MFB\EmailBundle\Form\VariableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\EmailBundle\Service\Template as EmailTemplateService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TemplatesController extends Controller
{

    /**
     * @Route("/select_variables", name="mfb_admin_select_variables")
     * @Template
     */
    public function selectVariablesAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $emailTemplateService = $this->get('mfb_email.template');

        $emailTemplate =$emailTemplateService->getEmailTemplate($this->getUserId());

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

            $emailTemplate =$emailTemplateService->getEmailTemplate($this->getUserId());
            $emailTemplateService->removesUnwantedVariables($emailTemplate);
            $emailTemplateService->addMandatoryVariables($emailTemplate);

            return $this->redirect($this->generateUrl('mfb_admin_edit_template'));
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("edit_template", name="mfb_admin_edit_template")
     * @Template("MFBAdminBundle:Templates:edit.html.twig")
     */

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accountId = $this->getUserId();

        $emailTemplateService = $this->get('mfb_email.template');

        $emailTemplate = $emailTemplateService->getEmailTemplate($accountId);
        $editForm = $this->createEditForm($emailTemplate);


        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $this->setTemporaryTemplate($emailTemplate->getTemplateCode());
            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setTemplateTypeId(EmailTemplateService::EMAIL_TEMPLATE_TYPE);

            $notUsedVariables = $emailTemplateService->getMandatoryAndUnusedVariables($emailTemplate);

            if (count($notUsedVariables) > 0) {
                $showErrors = 'The following variables were not used: '. implode(' , ', $notUsedVariables);
                return $this->showEmailTemplate($accountId, $showErrors);
            }

            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));

            $emailTemplate->setTemplateTypeId(EmailTemplateService::EMAIL_TEMPLATE_TYPE);
            $em->persist($emailTemplate);
            $em->flush();
        }

        return $this->showEmailTemplate($accountId);
    }


    /**
     * @Route("/edit_thankyou_template", name="mfb_admin_edit_thankyou_template")
     * @Template("MFBAdminBundle:Templates:edit.html.twig")
     */
    public function thankYouEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accountId = $this->getUserId();

        $thankYouTemplate = $this->get('mfb_email.template')->getThankYouTemplate($accountId);

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);

        $thankYouForm->handleRequest($request);
        if ($thankYouForm->isValid()) {
            $thankYouTemplate->setTemplateCode($this->plain2html($thankYouTemplate->getTemplateCode()));
            $thankYouTemplate->setTemplateTypeId(EmailTemplateService::THANKYOU_TEMPLATE_TYPE);
            $em->persist($thankYouTemplate);
            $em->flush();

            return $this->redirect($this->generateUrl('mfb_admin_edit_template'));
        }

        return $this->showEmailTemplate($accountId);
    }

    public function showEmailTemplate($accountId, $errors = null)
    {
        $emailTemplate = $this->get('mfb_email.template')->getEmailTemplate($accountId);
        $thankYouTemplate = $this->get('mfb_email.template')->getThankYouTemplate($accountId);

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);
        $editForm = $this->createEditForm($emailTemplate);

        $templateCode = $this->html2plain($emailTemplate->getTemplateCode());
        $temporaryTemplateCode = $this->getTemporaryTemplate();
        if ($temporaryTemplateCode) {
            $templateCode = $temporaryTemplateCode;
        }

        $variables = $this->get('mfb_email.variables')->getVariables($emailTemplate);
        $editForm->get('templateCode')->setData($templateCode);

        $savedTemplate = $this->html2plain($thankYouTemplate->getTemplateCode());
        $thankYouForm->get('templateCode')->setData($savedTemplate);
        $thankyou_variables = $this->get('mfb_email.variables')->getVariables($thankYouTemplate);

        return array(
                'errors' => $errors,
                'variables' => $variables,
                'entity' => $emailTemplate,
                'form' => $editForm->createView(),
                'thankyou_entity' => $thankYouTemplate,
                'form_thankyou' => $thankYouForm->createView(),
                'thankyou_variables' => $thankyou_variables,
        );
    }

    /**
     * @Route("/template/{emailTemplateId}/list_variables", name="mfb_admin_list_possible_variables")
     * @Template
     */
    public function listPossibleVariablesAction($emailTemplateId)
    {
        $variables = $this->get('mfb_email.variables')->getSelectedVariables($emailTemplateId);
        return array(
                'emailTemplateId' => $emailTemplateId,
                'variables' => $variables
        );
    }

    private function createEditForm(EmailTemplate $entity)
    {
        $form = $this->createForm(
            new EmailTemplateType(),
            $entity,
            array(
                'action' => $this->generateUrl('mfb_admin_edit_template', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        return $form;
    }

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
        return $this->get('mfb_email.template')->plain2html($text);
    }

    public function getUserId()
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();
        return $accountId;
    }

    private function getTemporaryTemplate()
    {
        $temporaryTemplateCodeArray = $this->getRequest()->getSession()->getFlashBag()->get('templateCode');
        $temporaryTemplateCode = array_pop($temporaryTemplateCodeArray);
        return $temporaryTemplateCode;
    }

    private function setTemporaryTemplate($templateCode)
    {
        $this->getRequest()->getSession()->getFlashBag()->set(
            'templateCode',
            $templateCode
        );
    }
}
