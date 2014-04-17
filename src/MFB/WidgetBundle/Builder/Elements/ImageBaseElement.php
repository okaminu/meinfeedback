<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Entity\Color;

class ImageBaseElement implements ElementInterface {

    protected $image;

    protected $imageLower;

    protected $backgroundColor;

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
        $this->image = imagecreatefrompng($this->getResource('widgetTemplate'));
        $this->imageLower = imagecreatefrompng($this->getResource('widgetLowerFragment'));

        $color = imagecolorallocate(
            $this->imageLower,
            $this->backgroundColor->getRed(),
            $this->backgroundColor->getGreen(),
            $this->backgroundColor->getBlue()
        );
        imagefill($this->imageLower, 5, 5, $color);
        imagecopy($this->image, $this->imageLower, 2, 195, 0, 0, 191, 95);
    }

    public function setBackgroundColor(Color $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
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
    public function getResource($name)
    {
        return $this->resources[$name];
    }

}