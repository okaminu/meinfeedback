<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepsAwareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SetupWizard
{
    private $setupSteps;

    private $stepEntityService;

    private $router;

    private $eventDispatcher;

    public function __construct($stepEntityService, Router $router, $setupStepsConfigurator, $eventDispatcher)
    {
        $this->stepEntityService = $stepEntityService;
        $this->router = $router;
        $this->setupSteps = $setupStepsConfigurator->getStepsConfig();
        $this->eventDispatcher = $eventDispatcher;
        ksort($this->setupSteps);
    }

    public function getNextStep()
    {
        reset($this->setupSteps);
        $step = current($this->setupSteps);
        $this->eventDispatcher->dispatch("post{$step['route']}");
        return $this->createRedirect($step['route']);
    }

    private function createRedirect($route)
    {
        return new RedirectResponse($this->router->generate($route));
    }
}
