<?php
namespace MFB\EmailBundle\Service;


use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\EmailBundle\Entity\EmailTemplateVariable;
use Symfony\Component\Translation\TranslatorInterface;

class Variables
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $allVariables = array(
        'link', 'firstname', 'lastname', 'sal', 'homepage',
        'service_name', 'service_date', 'reference_id'
    );

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function getAllVariables()
    {
        if ($this->allVariables !== null) {
            return $this->allVariables;
        }
        $this->allVariables = array(

        );

        return $this->allVariables;
    }


    public function getVariables(EmailTemplate $template)
    {
        $variables = array();
        foreach ($template->getVariables() as $emailVariable) {
            $variables[] = $emailVariable->getType();
        }
        return $this->decorateVariables($variables);
    }

    public function getPossibleVariables(EmailTemplate $template)
    {
        $variables = $this->allVariables;
        foreach ($template->getVariables() as $emailVariable) {
            $key = array_search($emailVariable->getType(), $variables);
            if (isset($variables[$key])) {
                unset($variables[$key]);
            }
        }
        return $this->decorateVariables($variables);
    }

    private function decorateVariables($list)
    {
        $variables = array();
        foreach ($list as $variable) {
            $variables[] = array(
                'type' => strtolower($variable),
                'name' => '#'.strtoupper($variable).'#',
                'description' => $this->translator->trans(
                    'email_variable_desc_'.strtolower($variable),
                    array('email_variable_desc_'.strtolower($variable) => '')
                ),
                'example' => $this->translator->trans(
                    'email_variable_example_'.strtolower($variable),
                    array(
                        'email_variable_example_'.strtolower($variable)=>''
                    )
                )
            );
        }
        return $variables;

    }
}
