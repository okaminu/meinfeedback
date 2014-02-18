<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\AdminBundle\Service\FormSetupPaths;
use MFB\SetupWizardBundle\WizardPathsAwareInterface;

class Configurator
{
    private $adminFormSetupPaths;

    public function __construct(FormSetupPaths $adminFormSetupPaths)
    {
        $this->adminFormSetupPaths = $adminFormSetupPaths;
    }

    public function configure(WizardPathsAwareInterface $service)
    {
        $service->setPaths($this->adminFormSetupPaths->getPaths());
    }
}
