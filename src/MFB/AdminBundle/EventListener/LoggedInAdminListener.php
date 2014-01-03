<?php
namespace MFB\AdminBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContext;
use MFB\ChannelBundle\Service\ChannelRatingCriteria as CriteriaService;

class LoggedInAdminListener
{
    private $router;
    
    private $securityContext;

    private $ratingCriteriaService;

    private $showFormRoute = 'mfb_admin_show_form_setup';

    private $saveFormRoute = 'mfb_admin_update_rating_criteria_select';

    public function __construct(
        Router $router,
        SecurityContext $securityContext,
        CriteriaService $ratingCriteriaService
    ) {
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->ratingCriteriaService = $ratingCriteriaService;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            if ($this->isCriterias($event)) {
                $url = $this->getRedirectUrl();
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserMissingCriterias()
    {
        return !$this->ratingCriteriaService->hasSelectedRatingCriterias(
            $this->getUser()->getId()
        );
    }

    /**
     * @param $route
     * @return bool
     */
    private function isUserNotInCriteriaForm($route)
    {
        return !(($route == $this->getSaveFormRoute()) || ($route == $this->getShowFormRoute()));
    }

    /**
     * @return string
     */
    private function getShowFormRoute()
    {
        return $this->showFormRoute;
    }

    /**
     * @return string
     */
    private function getSaveFormRoute()
    {
        return $this->saveFormRoute;
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
    private function isCriterias(GetResponseEvent $event)
    {
        return $this->isUserLoggenIn() &&
        $this->isUserNotInCriteriaForm($this->getRoute($event)) &&
        $this->isUserMissingCriterias();
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
