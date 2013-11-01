<?php
namespace MFB\EmailBundle\Service;


use MFB\EmailBundle\Entity\EmailTemplate;
use Symfony\Component\Translation\TranslatorInterface;

class Variables
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function getVariables(EmailTemplate $template)
    {
        $variables = array();
        foreach ($template->getVariables() as $emailVariable) {
            $variableType = $emailVariable->getType();
            $variables[] = array(
                    'name' => '#'.strtoupper($variableType).'#',
                    'description' => $this->translator->trans('email_variable_desc_'.strtolower($variableType)),
                    'example' => $this->translator->trans('email_variable_example_'.strtolower($variableType))
            );
        }
        return $variables;
    }
}
