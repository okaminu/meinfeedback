<?php


namespace MFB\EmailBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use MFB\CustomerBundle\Entity\Customer;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use MFB\EmailBundle\Template\TemplateFactory;
use MFB\EmailBundle\Template\ThankYouTemplate;
use Symfony\Component\Translation\TranslatorInterface;

class Template
{

    private $em;

    private $translator;

    private $emailTemplate;

    private $allVariables;

    const EMAIL_TEMPLATE_TYPE = 1;
    const THANKYOU_TEMPLATE_TYPE = 2;

    public function __construct(ObjectManager $em, TranslatorInterface $translator, $variables)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->allVariables = $variables;
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

        $repo = $this->em->getRepository('MFBEmailBundle:EmailTemplate');
        $repo->clear();
        $this->emailTemplate = $repo->findOneBy(
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

            foreach($this->allVariables['mandatory'] as $key => $value)
            {
                $this->addVariable($key, true);
            }

            foreach($this->allVariables['optional'] as $key => $value)
            {
                $this->addVariable($key);
            }

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

        $templateFactory =new TemplateFactory();
        $template = $templateFactory->get($name);

        return $template
            ->setContent($templateEntity->getTemplateCode())
            ->setCustomer($customer)
            ->getTranslation();
    }

    /**
     * Get thank you text
     *
     * @param Customer $customer
     * @return mixed
     *
     * @deprecated Use getText instead
     */
    public function getThankYouText(Customer $customer)
    {
        $templateEntity = $this->getThankYouTemplate($customer->getAccountId());

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
        return $this->translator->trans($map[$name]['default_text']);
    }

    private function templatesMap()
    {
        return array(
            'AccountChannel' => array(
                'templateTypeId' => self::EMAIL_TEMPLATE_TYPE,
                'default_text' => 'default_template_body'
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
    private function addVariable($type, $isActive = false)
    {
        $variable = new EmailTemplateVariable();
        $variable->setType($type);
        $variable->setIsActive($isActive);
        $variable->setEmailTemplate($this->emailTemplate);
        $this->emailTemplate->addVariable($variable);
    }

    /**
     * @return array
     */
    private function getAllVariables()
    {
        return array_merge($this->allVariables['mandatory'], $this->allVariables['optional']);
    }

    /**
     * Gets a list of unused variables
     * @param $emailTemplate
     * @return array
     */
    public function getMandatoryAndUnusedVariables($emailTemplate)
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
     * @param $emailTemplate
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

    public function addMandatoryVariables($emailTemplate)
    {
        /** @var EmailTemplate $emailTemplate  */
        $missingVariables = $this->getMandatoryAndUnusedVariables($emailTemplate);

        $templateWithMissingVars = $emailTemplate->getTemplateCode() .implode('<br>', $missingVariables);
        $emailTemplate->setTemplateCode($this->plain2html($templateWithMissingVars));
        $this->em->persist($emailTemplate);
        $this->em->flush();
    }

    public function removesUnwantedVariables($emailTemplate)
    {
        /** @var EmailTemplate $emailTemplate  */
        $inactiveVariables = $this->getVariables($emailTemplate, false);
        $allValues = $this->getAllVariables();
        $unwantedValueCodes = array();

        foreach ($inactiveVariables as $variable) {
            $unwantedValueCodes[] = $allValues[$variable->getType()];
        }

        $templateWithoutVars = str_replace($unwantedValueCodes, '', $emailTemplate->getTemplateCode());
        $emailTemplate->setTemplateCode($this->plain2html($templateWithoutVars));
        $this->em->persist($emailTemplate);
        $this->em->flush();
    }

    public function html2plain($html)
    {
        $converter = new \MFB\HtmlToText\Converter();
        return $converter->html2text($html);
    }

    public function plain2html($text)
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
