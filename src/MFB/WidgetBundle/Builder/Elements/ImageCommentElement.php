<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;
use MFB\WidgetBundle\Builder\Elements\RatingStarsElement;
use MFB\WidgetBundle\Builder\Elements\ImageTextElement;
use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageCommentElement extends AbstractImageBase  implements ElementInterface {

    protected  static $last_line_padding = 20;

    protected $lastLine = 222;

    protected static $spaceAfterComment = 8;

    protected static $spacerHeight = 4;

    protected static $baselineModifier = 10;

    protected $text;

    protected $boxWidth;

    protected $boxHeight;

    public function __construct($resources)
    {
        $this->setResources($resources);
    }

    public function render($image = null)
    {
        $this->setImage($image);
        $this->addComment($this->getText());
        return $this->getImage();
    }

    public function getName()
    {
        return 'comment';
    }

    public function isHeigher($maxHeight, $elementsHeight, $startingHeight = 0)
    {

        if ($startingHeight + $elementsHeight > $maxHeight) {
            return true;
        }

        return false;
    }

    public function getGroupHeight($elements)
    {
        $elementsHeight = 0;
        foreach($elements as $element)
        {
            $elementsHeight += $element->getElementHeight();
        }

        return $elementsHeight;
    }

    public function addComment($feedbacks)
    {

        foreach ($feedbacks as $feedback) {

                try {

                    $groupHeight = $this->getGroupHeight(
                        array(
                            $this->getStarsElement($feedback),
                            $this->getTextElement($feedback)
                        )
                    );

                    if ($this->isHeigher(
                        $this->getBoxHeight(),
                        $groupHeight,
                        $this->getPositionY()
                    ) == true)
                    {
                        break;
                    }

                    $positionY = $this->writeElements(
                        array(
                            $this->getStarsElement($feedback),
                            $this->getTextElement($feedback)
                        ),
                        $this->getPositionY(),
                        $this->getSpacerHeight()
                    );

                    $this->setPositionY($positionY + self::$spaceAfterComment);

                } catch (\Exception $ex) {
                    echo $ex->getMessage();
                }

        }

        return $this;
    }

    protected function increasePositionY($height)
    {
        $this->setPositionY($this->getPositionY() + $height);
    }

    public function writeElements($elements, $startingPositionY, $spacerHeight)
    {
        $render = $this->image;
        $positionY = $startingPositionY;

        foreach ($elements as $element)
        {
            $element->setPositionY($positionY);
            if ($element->getElementHeight() > 0) {
                $render = $element->render($render);
            }

            $positionY = $positionY + $element->getElementHeight() + $spacerHeight ;
        }

        return $positionY;
    }

    /**
     * Get Stars Element
     *
     * @param $feedback
     * @return $this
     *
     * @todo here we need dependency injection
     */
    protected function getStarsElement($feedback)
    {
        $element = new RatingStarsElement( $this->getResources() );

        if ($feedback->getRating() == null)
        {
            $element->setElementHeight(0);
        }

        return $element
            ->setPositionX($this->getPositionX())
            ->setPositionY($this->getPositionY())
            ->setRating($feedback->getRating());
    }

    protected function getTextElement($feedback)
    {
        $textElement = new ImageTextElement( $this->getResources() );

        $commentAdded = $this->wrap(
            $this->getFontSize(),
            $this->getFontType(),
            '"'. trim($feedback->getContent()) . '"',
            $this->getBoxWidth(),
            $this->getBoxHeight()
        );

        return $textElement
            ->setText($commentAdded)
            ->setPositionX($this->getPositionX())
            ->setPositionY($this->getPositionY())
            ->setFontColorCode(230, 230, 230);
    }

    public function getFontSize()
    {
        return 9;
    }

    public function getFontType()
    {
        return $this->getRecource('lucidaFontFile');
    }

    /**
     *
     * @return mixed
     */
    public function  getTextBaselineModifier()
    {
        return self::$baselineModifier;
    }


    protected function getSpacerHeight()
    {
        return self::$spacerHeight;
    }

    /**
     * Get text height. Because text is wrapped, we simply multiply wrap count by font size
     *
     * @param $text
     * @param $fontSize
     * @return int
     */
    public function  getTextHeight($text, $fontSize)
    {
        return substr_count($text, "\n") * ($fontSize);
    }

    public function getElementHeight($text, $fontSize, $fontFace)
    {
        $info = imagettfbbox($fontSize, 0, $fontFace, $text);
        return ($info[5] - $info[3]) * - 1;
    }

    /**
     * Wrap text to fit specified sized box
     *
     * @param $fontSize
     * @param $fontFace
     * @param $string
     * @param $width
     * @param $maxHeight
     * @return string
     * @throws \Exception
     */
    protected function wrap($fontSize, $fontFace, $string, $width, $maxHeight)
    {

        $ret = "";
        $arr = explode(" ", $string);

        foreach ($arr as $word) {
            $testboxWord = imagettfbbox($fontSize, 0, $fontFace, $word);

            // huge word larger than $width, we need to cut it internally until it fits the width
            $len = strlen($word);
            while ($testboxWord[2] > $width && $len > 0) {
                $word = substr($word, 0, $len);
                $len--;
                $testboxWord = imagettfbbox($fontSize, 0, $fontFace, $word);
            }

            $teststring = $ret.' '.$word;
            $testboxString = imagettfbbox($fontSize, 0, $fontFace, $teststring);
            if ($testboxString[2] > $width) {
                $ret.=($ret==""?"":"\n").$word;
            } else {
                $ret.=($ret==""?"":' ').$word;
            }
            if ($testboxString[3] > $maxHeight) {
                throw new \Exception('too large text');
            }
        }

        return $ret;
    }

    /**
     * @param mixed $boxWidth
     * @return $this;
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBoxWidth()
    {
        return $this->boxWidth;
    }

    /**
     * @param mixed $boxHeight
     * @return $this;
     */
    public function setBoxHeight($boxHeight)
    {
        $this->boxHeight = $boxHeight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBoxHeight()
    {
        return $this->boxHeight;
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