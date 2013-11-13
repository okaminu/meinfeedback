<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;
use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageTextElement extends AbstractImageBase implements ElementInterface {

    protected  static $last_line_padding = 20;

    protected $text;

    protected $fontColorTop;

    protected $fontColorBottom;

    protected $positionX;

    protected $positionY;

    public function __construct($resources)
    {
        $this->setResources($resources);

    }

    public function render($image = null)
    {
        $this->setImage($image);
        $this->writeText();
        return $this->getImage();
    }

    public function getName()
    {
        return 'text';
    }

    public function writeTextElement($text)
    {

        imagettftext(
            $this->image, //img to apply
            8, // size
            0, // angle
            $this->getPositionX(), // x
            $this->getPositionY() + 8, // y + baseline modifier. this is cordinate for baseline, so we adjust it
            $this->getFontColor(), // color
            $this->getFont(), // font file
            $text // text
        );

        return $this;
    }

    public function getElementHeight()
    {
        $info = imagettfbbox($this->getFontSize(), 0, $this->getFont(), $this->getText());
        return ($info[5] - $info[3]) * - 1;
    }

    public function writeText()
    {
        $this->writeTextElement($this->getText());
    }

    public function getFontSize()
    {
        return 8;
    }

    public function getFont()
    {
        return $this->getRecource('arialFontFile');
    }

    /**
     * @param mixed $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }
}