<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepsAwareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SetupWizard
{
    private $setupSteps;

    private $entityManager;

    private $router;

    public function __construct($entityManager, Router $router, $setupStepsConfigurator)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->setupSteps = $setupStepsConfigurator->getStepsConfig();
    }

    public function getNextStep()
    {
        return $this->createRedirect('lol');
    }

    private function createRedirect($route)
    {
        return new RedirectResponse($this->router->generate($route));
    }
}
