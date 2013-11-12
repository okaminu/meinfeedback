<?php


namespace MFB\WidgetBundle\Builder;


use MFB\WidgetBundle\Builder\Elements\ElementInterface;

use MFB\WidgetBundle\Builder\Elements\ImageBaseElement;
use MFB\WidgetBundle\Builder\Elements\ImageRepeatTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageCommentElement;


class ImageBuilder implements BuilderInterface{

    protected $layout;

    protected $resources;

    public function __construct($resources)
    {
        $this->setResources($resources);
    }

    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Add element to layout schema
     *
     * @param $block
     * @return $this
     */
    public function addElement(ElementInterface $block)
    {
        $this->layout[$block->getName()] = $block;
        return $this;
    }

    public function getElement($element)
    {
        return $this->layout[$element];
    }

    /**
     * Clone element
     *
     * @param $element
     * @return mixed
     *
     * @todo this needs a smarter way
     */
    public function cloneElement($element)
    {
        $clone =  clone $this->layout[$element];
        $this->layout[$clone->getName() . md5(time())] = $clone;
        return $clone;
    }

    public function createImage()
    {
        $base = null;
        foreach ( $this->layout as $element)
        {
            $base = $element->render($base);
        }

        ob_start();
        imagepng($element->getImage());
        $imageBlob = ob_get_contents();
        ob_end_clean();

        return $imageBlob;
    }

    /**
     * Get layout filled by objects
     * @return mixed
     */
    public function getFilledLayout()
    {
        $this
            ->addElement(new ImageBaseElement($this->resources))
            ->addElement(new ImageTextElement($this->resources))
            ->addElement(new ImageRepeatTextElement($this->resources))
            ->addElement(new ImageCommentElement($this->resources));

        return $this->getLayout();
    }

    /**
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }



} 