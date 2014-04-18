<?php
namespace MFB\WidgetBundle\Entity;

class Color
{
    protected $hex;

    protected $red;

    protected $green;

    protected $blue;

    public function __construct($hex = null, $red = null, $green = null, $blue = null)
    {
        $this->hex = $hex;
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    protected function hexToRGB()
    {
        $this->red = hexdec(substr($this->hex, 0, 2));
        $this->green = hexdec(substr($this->hex, 2, 2));
        $this->blue = hexdec(substr($this->hex, 4, 2));
    }

    protected function RGBToHex()
    {
        $this->hex = '';
        $this->hex .= dechex($this->red);
        $this->hex .= dechex($this->green);
        $this->hex .= dechex($this->blue);
    }

    public function getHex()
    {
        if (!$this->hex) {
            $this->RGBToHex();
        }
        return $this->hex;
    }

    public function getRed()
    {
        if (!$this->red) {
            $this->hexToRGB();
        }
        return $this->red;
    }

    public function getGreen()
    {
        if (!$this->green) {
            $this->hexToRGB();
        }
        return $this->green;
    }

    public function getBlue()
    {
        if (!$this->blue) {
            $this->hexToRGB();
        }
        return $this->blue;
    }

    public function setHex($hex)
    {
        $this->hex = $hex;
    }

    public function setRed($red)
    {
        $this->red = $red;
    }

    public function setGreen($green)
    {
        $this->green = $green;
    }

    public function setBlue($blue)
    {
        $this->blue = $blue;
    }

}