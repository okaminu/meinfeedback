<?php


namespace MFB\WidgetBundle\Builder\Elements;


abstract class AbstractImageBase {

    protected $image;

    protected $resources;

    public function __construct($resources)
    {
        $this->setResources($resources);
    }

    abstract protected function getName();

    abstract protected function render($image = null);

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
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

    /**
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }

}