<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;
use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageRepeatTextElement extends AbstractImageBase implements ElementInterface {

    protected  static $last_line_padding = 20;

    protected $lastLine;

    protected $text;

    protected $fontColorTop;

    protected $fontColorBottom;

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
        return 'repeatText';
    }

    public function writeTextElement($text)
    {
        imagettftext(
            $this->image, //img to apply
            8, // size
            0, // angle
            $this->getPositionX(), // x
            $this->lastLine, // y
            $this->fontColorBottom, // color
            $this->getRecource('arialFontFile'), // font file
            $text // text
        );

        $this->lastLine += self::$last_line_padding;

        return $this;
    }

    /**
     *
     */
    public function writeText()
    {
        foreach ($this->text as $text)
        {
            $this->writeTextElement($text);
        }
    }

    public function addText($text)
    {
        $this->text[] = $text;
        return $this;
    }

    /**
     * @param int $lastLine
     * @return $this
     */
    public function setLastLine($lastLine)
    {
        $this->lastLine = $lastLine;
        return $this;
    }


}