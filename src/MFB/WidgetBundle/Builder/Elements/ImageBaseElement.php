<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageBaseElement implements ElementInterface {

    protected $fontColorBottom;
    protected $fontColorTop;

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
        $this->fontColorTop = imagecolorallocate($this->image, 230, 230, 230);
        $this->fontColorBottom = imagecolorallocate($this->image, 108, 108, 108);
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