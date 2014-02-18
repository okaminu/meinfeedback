<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepsAwareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SetupWizard implements WizardStepsAwareInterface
{
    private $setupSteps;

    private $afterSetup;

    private $entityManager;

    private $router;

    public function __construct($entityManager, Router $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function setSteps($steps)
    {
        $this->setupSteps = $steps;
    }

    public function setAfterSetup($afterSetup)
    {
        $this->afterSetup = $afterSetup;
    }

    public function getNextStep($step)
    {
        $key = array_search($step, $this->setupSteps);

        $nextStepRoute = $this->afterSetup;
        if (isset($this->setupSteps[$key + 1])) {
            $nextStepRoute = $this->setupSteps[$key + 1];
        }
        return $this->createRedirect($nextStepRoute);
    }

    private function createRedirect($route)
    {
        return new RedirectResponse($this->router->generate($route));
    }
}
