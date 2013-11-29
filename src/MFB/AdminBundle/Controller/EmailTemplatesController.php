<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Form\EmailTemplateType;
use MFB\EmailBundle\Form\ThankYouTemplateType;
use MFB\EmailBundle\Form\VariableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmailTemplatesController extends Controller
{


    public function selectVariablesAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var EmailTemplate $emailTemplate  */
        $emailTemplate =$this->get('mfb_email.template')->getEmailTemplate($this->getUserId());

        $typesToFilter = array('firstname', 'service_name', 'service_date', 'service_id', 'customer_id','reference_id');

        $filteredVariables = $emailTemplate->getVariables()->filter(function($entry) use ($typesToFilter){
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

        $emailTemplate = $this->get('mfb_email.template')->getEmailTemplate($accountId);
        $editForm = $this->createEditForm($emailTemplate);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setThankYouCode($this->plain2html($emailTemplate->getThankYouCode()));
            $emailTemplate->setTemplateTypeId(2);
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

        $thankYouTemplate = $this->get('mfb_email.template')->getThankYouTemplate($accountId);

        $thankYouForm = $this->createThankYouForm($thankYouTemplate);

        $thankYouForm->handleRequest($request);
        if ($thankYouForm->isValid()) {
            $thankYouTemplate->setTemplateCode($this->plain2html($thankYouTemplate->getTemplateCode()));
            $thankYouTemplate->setTemplateTypeId(2);
            $em->persist($thankYouTemplate);
            $em->flush();

            return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
        }

        return $this->showEmailTemplate($accountId);
    }

    /**
     * @param $accountId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmailTemplate($accountId)
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
     * @param TemplateManagerInterface $templateManager
     * @param $type
     * @param $accountId
     * @return mixed
     */
    private function getEmailTemplate(TemplateManagerInterface $templateManager, $type, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $emailTemplate = $templateManager->getTemplate(
            $accountId,
            $type,
            'AccountChannel',
            $em,
            $this->get('translator')
        );
        return $emailTemplate;
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
}
