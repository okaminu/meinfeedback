<?php


namespace MFB\WidgetBundle\Builder\Elements;


class RatingStarsElement {

    protected $image;

    protected $stars;

    protected $starHeight;

    protected $starWidth;

    protected $rating;

    protected $positionX;

    protected $positionY;

    public function __construct($image, $rating, $resources)
    {
        $this->image = $image;

        $this->rating = $rating;

        $this->stars = $resources['stars'];

    }

    public function countStarSizes($image)
    {
        if (isset($this->starHeight) && isset($this->starWidth) ) return;

        list($WidgetRatingStarsCurrentWidth, $WidgetRatingStarsCurrentHeight) =  getimagesize($image);
        $this->setStarHeight($WidgetRatingStarsCurrentHeight);
        $this->setStarWidth($WidgetRatingStarsCurrentWidth);
    }

    public function createRatingStar()
    {
        $opacity = 100;

        $emptyStar = $this->stars['0'];
        $fullStar = $this->stars['1'];

        // Take the integer part of the rating(sum of fully stars).
        $StarSumFirstPart = $StarSum = (int)$this->rating;

        $WidgetRatingStarsPaddingLaft = $this->getPositionX();
        $this->countStarSizes($fullStar);

        if ($StarSumFirstPart != 0) {

            for ($i = 1; $i <= $StarSumFirstPart; $i++) {
                $WidgetRatingStarsCurrent = imageCreateFromGIF($fullStar);
                ImageAlphaBlending($WidgetRatingStarsCurrent, true);
                imageSaveAlpha($WidgetRatingStarsCurrent, true);

                imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getStarHeight(), $opacity);
                imageSaveAlpha($this->image, true);
                $WidgetRatingStarsPaddingLaft += $this->getStarWidth();
            }
        }

        // Take the fractional part of the rating(not fully star).
        $StarSumSecondPart = $this->rating - $StarSumFirstPart;

        if ($StarSumSecondPart != 0) {
            $StarSum += 1;
            switch (true) {
                case ($StarSumSecondPart > 0 && $StarSumSecondPart < 0.5) :
                    $WidgetRatingStarsCurrentPath = $this->stars['025'];
                    break;
                case ($StarSumSecondPart >= 0.5 && $StarSumSecondPart < 0.75) :
                    $WidgetRatingStarsCurrentPath = $this->stars['05'];
                    break;
                case ($StarSumSecondPart >= 0.75 && $StarSumSecondPart < 1) :
                    $WidgetRatingStarsCurrentPath = $this->stars['075'];
                    break;
            }

            $WidgetRatingStarsCurrent = imageCreateFromGIF($WidgetRatingStarsCurrentPath);
            ImageAlphaBlending($WidgetRatingStarsCurrent, true);
            imageSaveAlpha($WidgetRatingStarsCurrent, true);

            imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getStarHeight(), $opacity);
            imageSaveAlpha($this->image, true);

            $WidgetRatingStarsPaddingLaft += $this->getStarWidth();
        }

        // Finish, empty star(s).
        if ($StarSum != 5) {
            for ($i = $StarSum; $i < 5; $i++) {
                $WidgetRatingStarsCurrent = imageCreateFromGIF($emptyStar);
                ImageAlphaBlending($WidgetRatingStarsCurrent, true);
                imageSaveAlpha($WidgetRatingStarsCurrent, true);

                imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getStarHeight(), $opacity);
                imageSaveAlpha($this->image, true);
                $WidgetRatingStarsPaddingLaft += $this->getStarWidth();
            }
        }

        return $this;

    }

    public  function getImage()
    {
        return $this->image;
    }

    /**
     * @param $positionX
     * @return $this
     */
    public function setPositionX($positionX)
    {
        $this->positionX = $positionX;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPositionX()
    {
        return $this->positionX;
    }

    /**
     * @param $positionY
     * @return $this
     */
    public function setPositionY($positionY)
    {
        $this->positionY = $positionY;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPositionY()
    {
        return $this->positionY;
    }

    /**
     * @param mixed $starHeight
     */
    public function setStarHeight($starHeight)
    {
        $this->starHeight = $starHeight;
    }

    /**
     * @return mixed
     */
    public function getStarHeight()
    {
        return $this->starHeight;
    }

    /**
     * @param mixed $starWidth
     */
    public function setStarWidth($starWidth)
    {
        $this->starWidth = $starWidth;
    }

    /**
     * @return mixed
     */
    public function getStarWidth()
    {
        return $this->starWidth;
    }


} 