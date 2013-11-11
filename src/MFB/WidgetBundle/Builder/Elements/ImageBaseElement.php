<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageBaseElement implements ElementInterface {

    protected $image;

    public function __construct($resources)
    {
        $this->resources = $resources;
    }

    public function getName()
    {
        return 'base';
    }

    protected function createBase()
    {
        $this->image = imagecreatefrompng($this->getRecource('widgetTemplate'));
    }

    public function render()
    {
        $this->createBase();
        return $this->image;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getRecource($name)
    {
        return $this->resources[$name];
    }

}