<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardPathsAwareInterface;

class SetupWizard implements WizardPathsAwareInterface
{
    private $setupPaths;

    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setPaths($paths)
    {
        $this->setupPaths = $paths;
    }

}
