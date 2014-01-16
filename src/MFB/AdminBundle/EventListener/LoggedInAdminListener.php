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

    private $criteriaFormPaths;

    private $adminService;

    private $showFormRoute = 'mfb_admin_show_form_setup';

    public function __construct(
        Router $router,
        SecurityContext $securityContext,
        Admin $adminService,
        $criteriaFormPaths
    ) {
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->criteriaFormPaths = $criteriaFormPaths;
        $this->adminService = $adminService;
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

    /**
     * @param $route
     * @return bool
     */
    private function isUserNotInCriteriaForm($route)
    {
        if (in_array($route, $this->criteriaFormPaths)) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
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
        $this->isUserNotInCriteriaForm($this->getRoute($event)) &&
        $this->adminService->isMissingMandatorySettings($this->getUser()->getId());
    }

    /**
     * @return string
     */
    private function getRedirectUrl()
    {
        $url = $this->router->generate($this->showFormRoute, array(), Router::ABSOLUTE_URL);
        return $url;
    }
}
