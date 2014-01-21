<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Form\EmailTemplateType;
use MFB\EmailBundle\Form\ThankYouTemplateType;
use MFB\EmailBundle\Form\VariableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\EmailBundle\Service\Template;

class TemplatesController extends Controller
{


    public function selectVariablesAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $emailTemplateService = $this->get('mfb_email.template');

        /** @var EmailTemplate $emailTemplate  */
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
        return $this->render(
            'MFBAdminBundle:Templates:selectVariables.html.twig',
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

        $emailTemplateService = $this->get('mfb_email.template');

        /**
         * @var $emailTemplate \MFB\EmailBundle\Entity\EmailTemplate
         */
        $emailTemplate = $emailTemplateService->getEmailTemplate($accountId);
        $editForm = $this->createEditForm($emailTemplate);


        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $this->setTemporaryTemplate($emailTemplate->getTemplateCode());
            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setTemplateTypeId(Template::EMAIL_TEMPLATE_TYPE);

            $notUsedVariables = $emailTemplateService->getMandatoryAndUnusedVariables($emailTemplate);

            if(count($notUsedVariables) > 0){
                $showErrors = 'The following variables were not used: '. implode(' , ', $notUsedVariables);
                return $this->showEmailTemplate($accountId, $showErrors);
            }

            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));

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

            return $this->redirect($this->generateUrl('mfb_admin_edit_template'));
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

        return $this->render(
            'MFBAdminBundle:Templates:edit.html.twig',
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
        $variables = $this->get('mfb_email.variables')->getSelectedVariables($emailTemplateId);
        return $this->render(
            'MFBAdminBundle:Templates:possibleVariablesList.html.twig',
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
                'action' => $this->generateUrl('mfb_admin_edit_template', array('id' => $entity->getId())),
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
        return $this->get('mfb_email.template')->plain2html($text);
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
     * @return mixed
     */
    private function getTemporaryTemplate()
    {
        $temporaryTemplateCodeArray = $this->getRequest()->getSession()->getFlashBag()->get('templateCode');
        $temporaryTemplateCode = array_pop($temporaryTemplateCodeArray);
        return $temporaryTemplateCode;
    }

    /**
     * @param $templateCode
     */
    private function setTemporaryTemplate($templateCode)
    {
        $this->getRequest()->getSession()->getFlashBag()->set(
            'templateCode',
            $templateCode
        );
    }
}
