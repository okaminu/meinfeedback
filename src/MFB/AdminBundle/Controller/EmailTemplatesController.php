<?php

namespace MFB\AdminBundle\Controller;

use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Form\EmailTemplateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmailTemplatesController extends Controller
{
    public function editAction(Request $request)
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $emailTemplate = $em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => 'AccountChannel'
            )
        );
        if (!$emailTemplate) {
            $emailTemplate = new EmailTemplate();
            $emailTemplate->setTitle($this->get('translator')->trans('default_template_subject'));
            $emailTemplate->setTemplateCode($this->get('translator')->trans('default_template_body'));
            $emailTemplate->setThankYouCode($this->get('translator')->trans('default_template_thank_you'));
            $linkVariable = new EmailTemplateVariable();
            $linkVariable->setType('link');
            $linkVariable->setValue('');
            $linkVariable->setEmailTemplate($emailTemplate);
            $emailTemplate->addVariable($linkVariable);
        }
        $emailTemplate->setAccountId($accountId);
        $emailTemplate->setName('AccountChannel');

        $editForm = $this->createEditForm($emailTemplate);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $emailTemplate->setTemplateCode($this->plain2html($emailTemplate->getTemplateCode()));
            $emailTemplate->setThankYouCode($this->plain2html($emailTemplate->getThankYouCode()));
            $em->persist($emailTemplate);
            $em->flush();

            return $this->redirect($this->generateUrl('mfb_admin_edit_email_template'));
        }

        $editForm->get('templateCode')->setData($this->html2plain($emailTemplate->getTemplateCode()));
        $editForm->get('thankYouCode')->setData($this->html2plain($emailTemplate->getThankYouCode()));
        return $this->render(
            'MFBAdminBundle:EmailTemplates:edit.html.twig',
            array(
                'entity'      => $emailTemplate,
                'form'   => $editForm->createView(),
            )
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
