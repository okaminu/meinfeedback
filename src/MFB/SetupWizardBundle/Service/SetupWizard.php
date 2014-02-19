<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SetupWizard
{
    private $steps;

    private $stepEntityService;

    private $router;

    private $eventDispatcher;

    public function __construct($stepEntityService, Router $router, $stepsConfigurator, $eventDispatcher)
    {
        $this->stepEntityService = $stepEntityService;
        $this->router = $router;
        $this->steps = $stepsConfigurator->getStepsCollection();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getNextStepRedirect($channelId)
    {
//        $nextStep = $this->getNextPendingStep($channelId);

//        $this->dispatchStepAfterEvent($this->getCurrentStep($channelId));
//        $this->dispatchStepBeforeEvent($nextStep);

        return $this->createStepRedirect($this->getNextPendingStep($channelId));
    }

    public function dispatchStepAfterEvent(WizardStepInterface $step)
    {
        $this->eventDispatcher->dispatch("setupWizard.post{$step->getName()}");
    }

    public function dispatchStepBeforeEvent(WizardStepInterface $step)
    {
        $this->eventDispatcher->dispatch("setupWizard.pre{$step->getName()}");
    }

    private function getNextPendingStep($channelId)
    {
        $steps = $this->getPendingStepsSortedByPriority($channelId);
        return array_pop($steps);
    }

    private function createStepRedirect(WizardStepInterface $step)
    {
        return new RedirectResponse($this->router->generate($step->getRoute()));
    }

    private function getPendingSteps($channelId)
    {
        if (!$this->stepEntityService->hasPendingSteps($channelId)) {
            $this->createStoreAllSetupSteps($channelId);
        }
        return $this->stepEntityService->findPendingByChannelId($channelId);
    }

    private function createStoreAllSetupSteps($channelId)
    {
        foreach ($this->steps->getStepsArray() as $step) {
            $stepEntity = $this->stepEntityService->createNewPending($channelId);
            $stepEntity->setName($step->getName());
            $this->stepEntityService->store($stepEntity);
        }
    }

    private function getPendingStepsSortedByPriority($channelId)
    {
        $pendingSteps = $this->getPendingSteps($channelId);
        $stepNames = array();
        foreach ($pendingSteps as $pendingStep) {
            $stepNames[] = $pendingStep->getName();
        }
        $this->steps->sortByPriority();
        return $this->steps->getStepsByNames($stepNames);
    }
}
