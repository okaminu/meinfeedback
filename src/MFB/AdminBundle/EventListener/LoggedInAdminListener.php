<?php
namespace MFB\AdminBundle\EventListener;


use MFB\AdminBundle\Service\Admin;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
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

    private $adminService;

    private $showFormRoute;

    public function __construct(
        Router $router,
        SecurityContext $securityContext,
        Admin $adminService,
        $allowedActions,
        $allowedControllers,
        $showFormRoute
    ) {
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->showFormRoute = $showFormRoute;
        $this->allowedActions = array_merge($allowedActions, array($showFormRoute));
        $this->adminService = $adminService;
        $this->allowedControllers = $allowedControllers;

    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->shouldRedirect($event)) {
                $url = $this->getRedirectUrl();
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }

    private function isUserNotInCriteriaForm($route, $controller)
    {
        if (in_array($route, $this->allowedActions) || in_array($controller, $this->allowedControllers)) {
            return false;
        }
        return true;
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

    /**
     * @param GetResponseEvent $event
     * @return mixed
     */
    private function getRoute(GetResponseEvent $event)
    {
        return $event->getRequest()->get('_route');
    }

    /**
     * @param GetResponseEvent $event
     * @return bool
     */
    private function shouldRedirect(GetResponseEvent $event)
    {
        return $this->isUserLoggenIn() &&
        $this->isUserNotInCriteriaForm($this->getRoute($event), $this->getController($event)) &&
        $this->adminService->isMissingMandatorySettings($this->getUser()->getId());
    }

    /**
     * @return string
     */
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

}
