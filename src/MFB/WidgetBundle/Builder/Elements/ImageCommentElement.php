<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;
use MFB\WidgetBundle\Builder\Elements\RatingStarsElement;
use MFB\WidgetBundle\Builder\Elements\ElementInterface;

class ImageCommentElement extends AbstractImageBase  implements ElementInterface {

    protected  static $last_line_padding = 20;

    protected $lastLine = 222;

    protected $text;

    protected $fontColorTop;

    protected $fontColorBottom;

    protected $boxWidth;

    protected $boxHeight;

    public function __construct($resources)
    {
        $this->setResources($resources);

    }

    public function render($image = null)
    {
        $this->setImage($image);
        $this->fontColorTop = imagecolorallocate($this->image, 230, 230, 230);
        $this->fontColorBottom = imagecolorallocate($this->image, 108, 108, 108);
        $this->addComment($this->getText());
        return $this->getImage();
    }

    public function getName()
    {
        return 'comment';
    }

    public function addComment($feedbacks)
    {
        $font = $this->getRecource('lucidaFontFile');
        $fontSize = 9;

        $commentPositionY = 20;
        $comment = '';
        $paddingAfter = 9;
        $starHeight = 0;

        foreach ($feedbacks as $feedback) {

                try {

                    //this is quite complicated. needs deeper look.
                    $commentAdded = $this->wrap(
                        $fontSize,
                        $font,
                        '"'. trim($feedback->getContent()) . '"'."\n\n\n",
                        $this->getBoxWidth(),
                        $this->getBoxHeight()
                    );

                    $commentSize = $this->getTextBoxHeight($commentAdded, $fontSize, $font);

                    $nextCommentHeight = $commentPositionY + $starHeight + $commentSize + $paddingAfter;

                    if ($nextCommentHeight > 200 )
                    {
                        break;
                    }
                    $comment .= $commentAdded;

                    $rating = $feedback->getRating();
                    $starHeight = 0;

                    if (isset($rating)) {
                        $starHeight = $this->addStars($rating, $this->getPositionX(), $commentPositionY);
                    }

                    $commentPositionY = $commentPositionY + $starHeight + $commentSize + $paddingAfter;

                } catch (\Exception $ex) {

                }


        }
        if ($comment == '') {
            $comment = $this->wrap(
                9,
                $font,
                '"'. substr($feedbacks->getContent(), 0, 500).'.."',
                $this->getBoxWidth(),
                $this->getBoxHeight()
            );

        }

        imagettftext(
            $this->image, //img to apply
            9, // size
            0, // angle
            $this->getPositionX(), // x
            50, // y
            $this->fontColorTop, // color
            $font, // font file
            $comment // text
        );

        return $this;
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

    public function getTextBoxHeight($text, $fontSize, $fontFace)
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

    public function addStars($rating, $positionX, $positionY)
    {
        $element = new RatingStarsElement( $this->getResources() );
        $element
            ->setRating($rating)
            ->setPositionX($positionX)
            ->setPositionY($positionY)
            ->render($this->image);

        return $element->getStarHeight();
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

}