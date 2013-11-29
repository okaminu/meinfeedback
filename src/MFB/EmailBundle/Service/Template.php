<?php


namespace MFB\EmailBundle\Service;


use Doctrine\Common\Persistence\ObjectManager;
use MFB\CustomerBundle\Entity\Customer;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\ThankYouTemplate;
use Symfony\Component\Translation\TranslatorInterface;

class Template
{

    private $em;
    private $translator;

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

        $emailTemplate = $this->em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => $name
            )
        );

        if (!$emailTemplate) {

            $emailTemplate = new EmailTemplate();
            $emailTemplate->setAccountId($accountId);
            $emailTemplate->setName($name);
            $emailTemplate->setTemplateTypeId($templateTypeId);

            $emailTemplate->setTitle($this->translator->trans('default_template_subject'));
            $emailTemplate->setTemplateCode($this->getDefaultTemplateCode($name));
            $emailTemplate->setThankYouCode($this->translator->trans('default_template_thank_you'));
            $linkVariable = new EmailTemplateVariable();
            $linkVariable->setType('link');
            $linkVariable->setValue('');
            $linkVariable->setEmailTemplate($emailTemplate);
            $emailTemplate->addVariable($linkVariable);
            $this->em->persist($emailTemplate);
            $this->em->flush();
        }

        return $emailTemplate;
    }

    public function getThankYouText(Customer $customer)
    {
        $templateEntity = $this->getTemplate(
            $customer->getAccountId(),
            'ThankYouPage'
        );

        $template = new \MFB\Template\ThankYouTemplate();
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

    public function templatesMap()
    {
        return array(
            'AccountChannel' => array(
                array(
                    'templateTypeId' => self::EMAIL_TEMPLATE_TYPE,
                    'default_text' => 'default_template_thank_you_page'
                )
            ),
            'ThankTou' => array(
                array(
                    'templateTypeId' => self::THANKYOU_TEMPLATE_TYPE,
                    'default_text' => 'default_template_thank_you_page'
                )
            )
        );
    }
}
