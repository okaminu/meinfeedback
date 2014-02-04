<?php
namespace MFB\AdminBundle\EventListener;


use MFB\AdminBundle\Service\FormSetup;
use MFB\ChannelBundle\Service\Channel;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContext;

class LoggedInAdminListener
{
    private $router;
    
    private $securityContext;

    private $allowedActions;

    private $allowedControllers;

    private $formSetupService;

    private $showFormRoute;

    private $channelService;

    public function __construct(
        Router $router,
        SecurityContext $securityContext,
        FormSetup $setupService,
        $allowedActions,
        $allowedControllers,
        $showFormRoute,
        Channel $channelService
    ) {
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->showFormRoute = $showFormRoute;
        $this->allowedActions = array_merge($allowedActions, array($showFormRoute));
        $this->formSetupService = $setupService;
        $this->allowedControllers = $allowedControllers;
        $this->channelService = $channelService;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->shouldRedirect($event)) {
                $this->clearAllFormData();
                $url = $this->getRedirectUrl();
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }

    private function isUserInAllowedLocation($route, $controller)
    {
        if (in_array($route, $this->allowedActions) || in_array($controller, $this->allowedControllers)) {
            return true;
        }
        return false;
    }

    private function isUserLoggenIn()
    {
        if ($this->securityContext->getToken() != null) {
            return $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY');
        }
        return false;
    }


    private function getUser()
    {
        $user = $this->securityContext
            ->getToken()
            ->getUser();

        return $user;
    }

    private function getRoute(GetResponseEvent $event)
    {
        return $event->getRequest()->get('_route');
    }

    private function shouldRedirect(GetResponseEvent $event)
    {
        try {
            $result =  $this->isUserLoggenIn() &&
            !$this->isUserInAllowedLocation($this->getRoute($event), $this->getController($event)) &&
            $this->formSetupService->isMissingMandatorySettings($this->getUser()->getId());
        } catch (\Exception $ex) {
            $result = true;
        }
        return $result;
    }

    private function getRedirectUrl()
    {
        return $this->router->generate($this->showFormRoute, array(), Router::ABSOLUTE_URL);
    }

    private function getController(GetResponseEvent $event)
    {
        $fullController = $event->getRequest()->get('_controller');
        preg_match("/^(.*Bundle.*Controller).*Action$/", $fullController, $match);
        return $match[1];
    }

    private function clearAllFormData()
    {
        $this->formSetupService->removeAllSetupFormData($this->getUser()->getId());
    }
}
