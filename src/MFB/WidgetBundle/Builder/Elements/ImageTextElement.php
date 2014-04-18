<?php


namespace MFB\WidgetBundle\Builder\Elements;

use Foxrate\BaseWidgetBundle\Builder\Elements\ElementInterface;
use Foxrate\BaseWidgetBundle\Builder\Elements\TextElementInterface;

class ImageTextElement extends AbstractImageBase implements ElementInterface, TextElementInterface
{

    protected static $last_line_padding = 20;

    protected $text;

    protected $fontColorTop;

    protected $fontColorBottom;

    protected $positionX;

    protected $positionY;

    protected $fontSize = 8;

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
            $this->getFontSize(), // size
            0, // angle
            $this->getPositionX(), // x
            $this->getPositionY() + $this->getBaseLineModifier(), // y
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

    /**
     * @doc inherit
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function setFontSize($size)
    {
        $this->fontSize = $size;
        return $this;
    }

    public function getBaseLineModifier()
    {
        return $this->getFontSize();
    }

    public function getFont()
    {
        return $this->getResource('arialFontFile');
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