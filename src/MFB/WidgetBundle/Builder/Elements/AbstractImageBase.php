<?php


namespace MFB\WidgetBundle\Builder\Elements;

use Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractImageBase {

    protected $image;

    protected $resources;

    protected $fontColorCode = array();

    protected $fontColor;

    public function __construct($resources)
    {
        $this->setResources($resources);
    }

    abstract public function getName();

    abstract public function render($image = null);

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
     */  /**
 * @param $positionX
 * @return $this
 */
    public function setPositionX($positionX)
    {
        $this->positionX = $positionX;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPositionX()
    {
        return $this->positionX;
    }

    /**
     * @param mixed $positionY
     * @return $this
     */
    public function setPositionY($positionY)
    {
        $this->positionY = $positionY;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPositionY()
    {
        return $this->positionY;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function setFontColorCode($red, $green, $blue)
    {
        $this->fontColorCode = array($red, $green, $blue);
        return $this;
    }

    /**
     * @param mixed $fontColor
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
    }

    /**
     * @return int
     * @throws \Symfony\Component\Config\Definition\Exception\Exception
     */
    public function getFontColor()
    {
        if (!isset($this->fontColor))
        {
            if (empty($this->fontColorCode))
            {
                throw new Exception('Font Color Code not given!');
            }

            list($red, $green, $blue) = $this->fontColorCode;
            $this->fontColor = imagecolorallocate($this->image, $red, $green, $blue);
        }

        return $this->fontColor;
    }

}