<?php


namespace MFB\WidgetBundle\Builder\Elements;

class ImageRepeatTextElement extends AbstractImageBase implements ElementInterface, TextElementInterface {

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
            $this->getFontSize(), // size
            0, // angle
            $this->getPositionX(), // x
            $this->lastLine + $this->getBaseLineModifier(), // y
            $this->getFontColor(), // color
            $this->getFont(), // font file
            $text // text
        );

        $this->lastLine += self::$last_line_padding;

        return $this;
    }

    public function getFont()
    {
        return $this->getResource('arialFontFile');
    }

    public function getFontSize()
    {
        return 8;
    }

    public function getBaseLineModifier()
    {
        return $this->getFontSize();
    }

    /**
     * Write all text elements
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