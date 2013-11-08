<?php


namespace MFB\WidgetBundle\Generator;



class ImageBuilder {

    protected $resources;

    protected $image;

    protected $fontColorBottom;

    protected $fontColorTop;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    protected function createImage()
    {
        $this->image = imagecreatefrompng($this->getRecource('widgetTemplate'));
        $this->fontColorTop = imagecolorallocate($this->image, 230, 230, 230);
        $this->fontColorBottom = imagecolorallocate($this->image, 108, 108, 108);
    }

    public function build($feedbacks, $feedbackCount)
    {

        $this->createImage();

        $this
            ->addDate( $feedbacks )
            ->addComment($feedbacks)
            ->addReviewCount($feedbackCount)
            ;

        ob_start();
        imagepng($this->getImage());
        $imageBlob = ob_get_contents();
        ob_end_clean();

        return $imageBlob;
    }

    public function addComment($feedbacks)
    {
        $font = $this->getRecource('lucidaFontFile');

        $comment = '';
        foreach ($feedbacks as $feedback) {
            try {
                $comment = $this->wrap(
                    9,
                    $font,
                    $comment . '"'.$feedback->getContent().'"'."\n\n",
                    170,
                    170
                );
            } catch (\Exception $ex) {

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
            40, // y
            $this->fontColorTop, // color
            $font, // font file
            $comment // text
        );

        return $this;
    }

    public function  addDate($feedbacks)
    {
        imagettftext(
            $this->image, //img to apply
            8, // size
            0, // angle
            120, // x
            222, // y
            $this->fontColorBottom, // color
            $this->getRecource('arialFontFile'), // font file
            $feedbacks->getCreatedAt()->format('d.m.Y') // text
        );

        return $this;
    }

    public function addReviewCount($feedbackCount)
    {
        imagettftext(
            $this->image, //img to apply
            8, // size
            0, // angle
            10, // x
            222, // y
            $this->fontColorBottom, // color
            $this->getRecource('arialFontFile'), // font file
            $feedbackCount.' Bewertungen' // text
        );

        return $this;
    }

    public function addReviewCountTranslated($feedbackCount)
    {
         imagettftext(
            $$this->image, //img to apply
            9, // size
            0, // angle
            10, // x
            180, // y
            $this->fontColorTop, // color
            $this->getRecource('lucidaFontFile'), // font file
                $this->get('translator')->transChoice(
                '1 review | %count% reviews',
                $feedbackCount,
                array('%count%' => $feedbackCount)
            ) // text
        );

        return $this;
    }

    /**
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getRecource($name)
    {
        return $this->resources[$name];
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

} 