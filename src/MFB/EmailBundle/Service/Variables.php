<?php
namespace MFB\EmailBundle\Service;


use Doctrine\ORM\EntityManager;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use Symfony\Component\Translation\TranslatorInterface;

class Variables
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $entityManager;

    private $mandatoryVariables;

    private $optionalVariables;


    public function __construct(TranslatorInterface $translator, EntityManager $entityManager, $variables)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->mandatoryVariables = $variables['mandatory'];
        $this->optionalVariables = $variables['optional'];
    }

    protected function getAllVariables()
    {
        $allVariables = array_merge(
            array_keys($this->mandatoryVariables),
            array_keys($this->optionalVariables)
        );
        return $allVariables;
    }

    protected function getAllVariableCodes()
    {
        $allVariables = array_merge(
            $this->mandatoryVariables,
            $this->optionalVariables
        );
        return $allVariables;
    }
    public function getVariables(EmailTemplate $template)
    {
        $variables = array();
        foreach ($template->getVariables() as $emailVariable) {
            $variables[] = $emailVariable->getType();
        }
        return $this->decorateVariables($variables);
    }

    public function getSelectedVariables($templateId)
    {
        $emailTemplateVariables = $this->entityManager->getRepository('MFBEmailBundle:EmailTemplateVariable')->findBy(
            array(
                'emailTemplate' => $templateId, 'isActive' => true
            )
        );
        $variableNames = array();
        foreach ($emailTemplateVariables as $variableEntity) {
            $variableNames[] = $variableEntity->getType();
        }
        return $this->decorateVariables($variableNames);
    }

    private function decorateVariables($list)
    {
        $variables = array();
        $allVariables = $this->getAllVariableCodes();
        foreach ($list as $variableName) {
            $variables[] = array(
                'type' => strtolower($variableName),
                'name' => $allVariables[$variableName],
                'description' => $this->translator->trans(
                    'email_variable_desc_'.strtolower($variableName),
                    array('email_variable_desc_'.strtolower($variableName) => '')
                ),
                'example' => $this->translator->trans(
                    'email_variable_example_'.strtolower($variableName),
                    array(
                        'email_variable_example_'.strtolower($variableName)=>''
                    )
                )
            );
        }
        return $variables;

    }
}
