<?php

namespace MFB\Template\Manager;

use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;

class TemplateManager {

    const EMAIL_TEMPLATE_TYPE = 1;
    const THANKYOU_TEMPLATE_TYPE = 2;

    public function getTemplate($accountId, $templateTypeId, $name, ObjectManager $em, TranslatorInterface $translator)
    {

        $emailTemplate = $em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => $name,
                'templateTypeId' => $templateTypeId
            )
        );

        if (!$emailTemplate) {

            $emailTemplate = new EmailTemplate();
            $emailTemplate->setAccountId($accountId);
            $emailTemplate->setName($name);
            $emailTemplate->setTemplateTypeId($templateTypeId);

            $emailTemplate->setTitle($translator->trans('default_template_subject'));
            $emailTemplate->setTemplateCode($this->getDefaultTemplateCode($translator, $templateTypeId));
            $emailTemplate->setThankYouCode($translator->trans('default_template_thank_you'));
            $linkVariable = new EmailTemplateVariable();
            $linkVariable->setType('link');
            $linkVariable->setValue('');
            $linkVariable->setEmailTemplate($emailTemplate);
            $emailTemplate->addVariable($linkVariable);
            $em->persist($emailTemplate);
            $em->flush();
        }

        return $emailTemplate;
    }

    /**
     * Hardcoded method for default text
     *
     * @param TranslatorInterface $translator
     * @param $templateType
     * @return string
     */
    protected function getDefaultTemplateCode(TranslatorInterface $translator, $templateType)
    {
        switch($templateType)
        {
            case(self::EMAIL_TEMPLATE_TYPE):
                return $translator->trans('default_template_body');
                break;
            case(self::THANKYOU_TEMPLATE_TYPE):
                return $translator->trans('default_template_thank_you_page');
                break;
        }
    }
} 