<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;

class ImageTextElement extends AbstractImageBase {

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
        $this->fontColorTop = imagecolorallocate($this->image, 230, 230, 230);
        $this->fontColorBottom = imagecolorallocate($this->image, 108, 108, 108);
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
            $this->getPositionY(), // y
            $this->fontColorBottom, // color
            $this->getRecource('arialFontFile'), // font file
            $text // text
        );

        return $this;
    }

    public function writeText()
    {
        $this->writeTextElement($this->getText());
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