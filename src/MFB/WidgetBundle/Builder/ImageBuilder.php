<?php


namespace MFB\WidgetBundle\Builder;


use MFB\WidgetBundle\Builder\Elements\ElementInterface;

use MFB\WidgetBundle\Builder\ElementComposite;

class ImageBuilder implements BuilderInterface
{
    protected $resources;

    protected $elementComposite;

    public function __construct($resources)
    {
        $this->setResources($resources);
        $this->elementComposite = new ElementComposite();
    }

    /**
     * Add element to layout schema
     *
     * @param $block
     * @return $this
     */
    public function addElement(ElementInterface $block)
    {
        $this->elementComposite->add($block);
        return $this;
    }

    public function createImage()
    {
        $base = $this->elementComposite->render();

        ob_start();
        imagepng($base);
        $imageBlob = ob_get_contents();
        ob_end_clean();

        return $imageBlob;
    }

    public function getLayout()
    {
        return $this->elementComposite;
    }
    /**
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    public function getResources()
    {
        return $this->resources;
    }


}
