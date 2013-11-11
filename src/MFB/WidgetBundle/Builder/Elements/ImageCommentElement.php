<?php


namespace MFB\WidgetBundle\Builder\Elements;

use MFB\WidgetBundle\Builder\Elements\AbstractImageBase;
use MFB\WidgetBundle\Builder\Elements\RatingStarsElement;

class ImageCommentElement extends AbstractImageBase {

    protected  static $last_line_padding = 20;

    protected $lastLine = 222;

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
        $paddingAfter = 5;

        foreach ($feedbacks as $feedback) {
            if ($commentPositionY < 100)
            {
                try {
                    $comment = $this->wrap(
                        $fontSize,
                        $font,
                        $comment . '"'.$feedback->getContent().'"'."\n\n\n",
                        170,
                        170
                    );

                    $rating = $feedback->getRating();
                    $starHeight = 0;

                    if (isset($rating)) {
                        $starHeight = $this->addStars($rating, 10, $commentPositionY);
                    }

                    $commentSize = $this->getTextHeight($comment, $fontSize);
                    $commentPositionY = $commentPositionY + $starHeight + $commentSize + $paddingAfter;

                } catch (\Exception $ex) {

                }
            }

        }
        if ($comment == '') {
            $comment = $this->wrap(
                9,
                $font,
                '"'. substr($feedbacks->getContent(), 0, 500).'.."',
                170,
                170
            );

        }

        imagettftext(
            $this->image, //img to apply
            9, // size
            0, // angle
            10, // x
            50, // y
            $this->fontColorTop, // color
            $font, // font file
            $comment // text
        );

        return $this;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
        $element = new RatingStarsElement($this->image, $rating, $this->getResources());
        $element
            ->setPositionX($positionX)
            ->setPositionY($positionY)
            ->createRatingStar();

        return $element->getStarHeight();
    }

}