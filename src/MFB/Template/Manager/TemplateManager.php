<?php

namespace MFB\Template\Manager;

use MFB\Template\Interfaces\TemplateManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\Template\ThankYouTemplate;

class TemplateManager implements TemplateManagerInterface
{

    const EMAIL_TEMPLATE_TYPE = 1;
    const THANKYOU_TEMPLATE_TYPE = 2;

    /**
     * @var $emailTemplate \MFB\EmailBundle\Entity\EmailTemplate
     */
    private $emailTemplate;

    public function getTemplate($accountId, $templateTypeId, $name, ObjectManager $em, TranslatorInterface $translator)
    {

        $this->emailTemplate = $em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => $name,
                'templateTypeId' => $templateTypeId
            )
        );

        if (!$this->emailTemplate) {

            $this->emailTemplate = new EmailTemplate();
            $this->emailTemplate->setAccountId($accountId);
            $this->emailTemplate->setName($name);
            $this->emailTemplate->setTemplateTypeId($templateTypeId);

            $this->emailTemplate->setTitle($translator->trans('default_template_subject'));
            $this->emailTemplate->setTemplateCode($this->getDefaultTemplateCode($translator, $templateTypeId));
            $this->emailTemplate->setThankYouCode($translator->trans('default_template_thank_you'));


            $this->addVariable('link', 1);
            $this->addVariable('lastname', 1);
            $this->addVariable('email', 1);
            $this->addVariable('salutation', 1);
            $this->addVariable('homepage', 1);
            $this->addVariable('firstname');
            $this->addVariable('service_name');
            $this->addVariable('service_date');
            $this->addVariable('reference_id');
            $this->addVariable('customer_id');
            $this->addVariable('service_id');

            $em->persist($this->emailTemplate);
            $em->flush();
        }

        return $this->emailTemplate;
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

    public function getThankYouText($em, $accountId, $customer, $translator)
    {
        $templateEntity = $this->getTemplate(
            $accountId,
            $this::THANKYOU_TEMPLATE_TYPE,
            'ThankYouPage',
            $em,
            $translator
        );

        $template = new ThankYouTemplate();
        $templateText = $template
            ->setContent($templateEntity->getTemplateCode())
            ->setCustomer($customer)
            ->getTranslation();
        return $templateText;
    }

    /**
     * @param $type
     * @param $isActive
     */
    private function addVariable($type, $isActive = 0)
    {

        $variable = new EmailTemplateVariable();
        $variable->setType($type);
        $variable->setIsActive($isActive);
        $variable->setEmailTemplate($this->emailTemplate);
        $this->emailTemplate->addVariable($variable);
    }
} 