<?php


namespace MFB\EmailBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use MFB\CustomerBundle\Entity\Customer;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Template\ThankYouTemplate;
use Symfony\Component\Translation\TranslatorInterface;

class Template
{

    private $em;

    private $translator;

    private $emailTemplate;

    const EMAIL_TEMPLATE_TYPE = 1;
    const THANKYOU_TEMPLATE_TYPE = 2;

    public function __construct(ObjectManager $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function createTemplate($accountId, $templateType)
    {
        return $this->getTemplate(
            $accountId,
            $templateType
        );
    }

    public function getTemplate($accountId, $name)
    {

        $templateTypeId = $this->getTemplateIdByName($name);

        $this->emailTemplate = $this->em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => $name
            )
        );

        if (!$this->emailTemplate) {

            $this->emailTemplate = new EmailTemplate();
            $this->emailTemplate->setAccountId($accountId);
            $this->emailTemplate->setName($name);
            $this->emailTemplate->setTemplateTypeId($templateTypeId);

            $this->emailTemplate->setTitle($this->translator->trans('default_template_subject'));
            $this->emailTemplate->setTemplateCode($this->getDefaultTemplateCode($name));
            $this->emailTemplate->setThankYouCode($this->translator->trans('default_template_thank_you'));
            $linkVariable = new EmailTemplateVariable();
            $linkVariable->setType('link');
            $linkVariable->setValue('');
            $linkVariable->setEmailTemplate($this->emailTemplate);
            $this->emailTemplate->addVariable($linkVariable);


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

            $this->em->persist($this->emailTemplate);
            $this->em->flush();
        }

        return $this->emailTemplate;
    }

    /**
     * @param $accountId
     * @return EmailTemplate
     */
    public function getEmailTemplate($accountId)
    {
        return $this->getTemplate(
            $accountId,
            'AccountChannel'
        );
    }

    /**
     * @param $accountId
     * @return EmailTemplate
     */
    public function getThankYouTemplate($accountId)
    {

        return $this->getTemplate(
            $accountId,
            'ThankYou'
        );
    }

    public function getText(Customer $customer, $name)
    {
        $templateEntity = $this->getTemplate(
            $customer->getAccountId(),
            $name
        );

        //todo Create Factory
        $templateClass = $name . 'Template';
        $template = new $templateClass();

        return $template
            ->setContent($templateEntity->getTemplateCode())
            ->setCustomer($customer)
            ->getTranslation();
    }

    public function getThankYouText(Customer $customer)
    {
        $templateEntity = $this->getTemplate(
            $customer->getAccountId(),
            'ThankYou'
        );

        $template = new ThankYouTemplate();
        $templateText = $template
            ->setContent($templateEntity->getTemplateCode())
            ->setCustomer($customer)
            ->getTranslation();
        return $templateText;
    }

    public function getTemplateIdByName($name)
    {

        $map = $this->templatesMap();
        return $map[$name]['templateTypeId'];
    }

    public function getDefaultTemplateCode($name)
    {
        $map = $this->templatesMap();
        return $map[$name]['default_text'];
    }

    private function templatesMap()
    {
        return array(
            'AccountChannel' => array(
                'templateTypeId' => self::EMAIL_TEMPLATE_TYPE,
                'default_text' => 'default_template_email_page'
            ),
            'ThankYou' => array(
                'templateTypeId' => self::THANKYOU_TEMPLATE_TYPE,
                'default_text' => 'default_template_thank_you_page'
            )
        );
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
