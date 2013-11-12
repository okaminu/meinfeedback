<?php

namespace MFB\AdminBundle;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener implements EventSubscriberInterface
{

    protected $hostMap;

    public function __construct($hostMap)
    {
        $this->hostMap = $hostMap;
    }

    /**
     * Set default locale
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // or use $request->getHost();
        $locale = $this->getMappedLocale($request->getHttpHost());

        $request->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }

    /**
     * Get locale by host from host map passed from service arguments
     *
     * @param $currentHost
     * @return mixed
     */
    protected function getMappedLocale($currentHost)
    {
        $locale = $this->hostMap['default'];

        foreach ($this->hostMap as $host => $hostLocale)
        {
            if ($currentHost == $host)
            {
                $locale = $hostLocale;
            }
        }

        return $locale;
    }
}