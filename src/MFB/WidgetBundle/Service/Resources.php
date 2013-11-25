<?php


namespace MFB\WidgetBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Resources
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getResources()
    {
        return array(
            'widgetTemplate' => $this->container->
                    get('kernel')->locateResource('@MFBWidgetBundle/Resources/widgets/n1.png'),
            'arialFontFile' => $this->container
                    ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Arial_Bold.ttf'),
            'lucidaFontFile' => $this->container
                    ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Lucida_Grande.ttf'),
            'stars' => array(
                '0' => $this->container
                        ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_0.gif'),
                '025' => $this->container
                        ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_025.gif'),
                '05' => $this->container
                        ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_05.gif'),
                '075' => $this->container
                        ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_075.gif'),
                '1' => $this->container
                        ->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_1.gif'),
            )
        );
    }
} 