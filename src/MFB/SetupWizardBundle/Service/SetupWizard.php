<?php
namespace MFB\SetupWizardBundle\Service;

use MFB\SetupWizardBundle\WizardStepInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use MFB\SetupWizardBundle\Event\StepEvent;

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

    public function getCurrentStepRedirect($channelId)
    {
        $this->dispatchStepBeforeEvent($this->getNextPendingStep($channelId), $channelId);
        return $this->createStepRedirect($this->getNextPendingStep($channelId));
    }

    public function completeGetNextPendingStep($channelId)
    {
        $this->dispatchStepAfterEvent($this->getNextPendingStep($channelId), $channelId);
        $this->dispatchStepBeforeEvent($this->getNextPendingStep($channelId), $channelId);

        return $this->createStepRedirect($this->getNextPendingStep($channelId));
    }

    public function dispatchStepAfterEvent(WizardStepInterface $step, $channelId)
    {
        $this->eventDispatcher->dispatch("setupWizard.after{$step->getName()}", new StepEvent($channelId));
    }

    public function dispatchStepBeforeEvent(WizardStepInterface $step, $channelId)
    {
        $this->eventDispatcher->dispatch("setupWizard.before{$step->getName()}", new StepEvent($channelId));
    }

    private function getNextPendingStep($channelId)
    {
        $steps = $this->getPendingStepsSortedByPriority($channelId);
        return array_shift($steps);
    }

    private function createStepRedirect(WizardStepInterface $step)
    {
        return new RedirectResponse($this->router->generate($step->getRoute()));
    }

    private function getPendingSteps($channelId)
    {
        if (!$this->stepEntityService->hasSteps($channelId)) {
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
