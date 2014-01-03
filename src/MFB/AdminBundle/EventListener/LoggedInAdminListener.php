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
            $this->redirectAdminIfMissingCriterias($event);
        }
    }

    /**
     * @param $showFormRoute
     * @return RedirectResponse
     */
    private function getRedirectResponse($showFormRoute)
    {
        $url = $this->router->generate($showFormRoute, array(), Router::ABSOLUTE_URL);
        return new RedirectResponse($url);
    }

    /**
     * @param $userId
     * @return bool
     */
    private function isUserMissingCriterias($userId)
    {
        return !$this->ratingCriteriaService->hasSelectedRatingCriterias($userId);
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
        return 'mfb_admin_show_form_setup';
    }

    /**
     * @return string
     */
    private function getSaveFormRoute()
    {
        return 'mfb_admin_update_rating_criteria_select';
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

    /**
     * @param GetResponseEvent $event
     */
    private function redirectAdminIfMissingCriterias(GetResponseEvent $event)
    {
        if ($this->isUserLoggenIn() &&
            $this->isUserNotInCriteriaForm($this->getRoute($event)) &&
            $this->isUserMissingCriterias($this->getUserId())
        ) {
                $this->addRedirectToEvent($event);
        }
    }

    private function getUserId()
    {
        $userId = $this->securityContext
            ->getToken()
            ->getUser()
            ->getId();

        return $userId;
    }


    /**
     * @param GetResponseEvent $event
     */
    private function addRedirectToEvent(GetResponseEvent $event)
    {
        $event->setResponse(
            $this->getRedirectResponse($this->getShowFormRoute())
        );
    }

    /**
     * @param GetResponseEvent $event
     * @return mixed
     */
    private function getRoute(GetResponseEvent $event)
    {
        return $event->getRequest()->get('_route');
    }
}
