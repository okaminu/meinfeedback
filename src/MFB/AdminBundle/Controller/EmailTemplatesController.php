<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Form\EmailTemplateType;
use MFB\EmailBundle\Form\ThankYouTemplateType;
use MFB\Template\Manager\TemplateManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmailTemplatesController extends Controller
{

    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $templateManager = new TemplateManager();
        $emailTemplate = $templateManager->getTemplate(
            $accountId,
            $templateManager::EMAIL_TEMPLATE_TYPE,
            'AccountChannel',
            $em,
            $this->get('translator')
        );
        $editForm = $this->createEditForm($emailTemplate);

        //dirty quick around for two forms handling
        if ($request->get('email_template', null) == true) {
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
                $emailTemplate->setThankYouCode($this->plain2html($emailTemplate->getThankYouCode()));
                $emailTemplate->setTemplateTypeId($templateManager::EMAIL_TEMPLATE_TYPE);
                $em->persist($emailTemplate);
                $em->flush();

                return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
            }

        }
        $editForm->get('templateCode')->setData($this->html2plain($emailTemplate->getTemplateCode()));
        $editForm->get('thankYouCode')->setData($this->html2plain($emailTemplate->getThankYouCode()));

        $thankYouTemplate = $templateManager->getTemplate(
            $accountId,
            $templateManager::THANKYOU_TEMPLATE_TYPE,
            'ThankYouPage',
            $em,
            $this->get('translator')
        );

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);
        $variables = $this->get('mfb_email.variables')->getVariables($emailTemplate);

        //dirty quick around for two forms handling
        if ($request->get('thankyou_template', null) == true) {
            $thankYouForm->handleRequest($request);
            if ($thankYouForm->isValid()) {
                $thankYouTemplate->setTemplateCode($this->plain2html($thankYouTemplate->getTemplateCode()));
                $thankYouTemplate->setTemplateTypeId($templateManager::THANKYOU_TEMPLATE_TYPE);
                $em->persist($thankYouTemplate);
                $em->flush();

                return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
            }
        }

        $thankYouForm->get('templateCode')->setData($this->html2plain($thankYouTemplate->getTemplateCode()));
        $thankyou_variables = $this->get('mfb_email.variables')->getVariables($thankYouTemplate);

        return $this->render(
            'MFBAdminBundle:EmailTemplates:edit.html.twig',
            array(
                'variables' => $variables,
                'entity'      => $emailTemplate,
                'form'   => $editForm->createView(),
                'thankyou_entity'      => $thankYouTemplate,
                'form_thankyou'   => $thankYouForm->createView(),
                'thankyou_variables'   => $thankyou_variables,
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

    public function addVariableAction($emailTemplateId, $variableType)
    {
        $em = $this->getDoctrine()->getManager();
        $emailTemplate = $em->find('MFBEmailBundle:EmailTemplate', $emailTemplateId);

        $linkVariable = new EmailTemplateVariable();
        $linkVariable->setType($variableType);
        $linkVariable->setValue('');
        $linkVariable->setEmailTemplate($emailTemplate);
        $em->persist($linkVariable);
        $em->flush();

        return $this->redirect(
            $this->generateUrl('mfb_admin_list_possible_variables', array('emailTemplateId' => $emailTemplateId))
        );
    }

    /**
     * Creates a form to edit a EmailTemplate entity.
     *
     * @param EmailTemplate $entity The entity
     *
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
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createThankYouForm(EmailTemplate $entity)
    {
        $form = $this->createForm(
            new ThankYouTemplateType(),
            $entity,
            array(
                'action' => $this->generateUrl('mfb_admin_edit_email_template', array('id' => $entity->getId())),
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





}
