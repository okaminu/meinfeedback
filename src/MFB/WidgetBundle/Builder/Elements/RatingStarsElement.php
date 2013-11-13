<?php


namespace MFB\WidgetBundle\Builder\Elements;

class RatingStarsElement extends AbstractImageBase implements ElementInterface{

    protected $stars;

    protected $elementHeight;

    protected $starWidth;

    protected $rating;

    public function countStarSizes($image)
    {
        if (isset($this->elementHeight) && isset($this->starWidth) ) return;

        list($WidgetRatingStarsCurrentWidth, $WidgetRatingStarsCurrentHeight) =  getimagesize($image);
        $this->setElementHeight($WidgetRatingStarsCurrentHeight);
        $this->setStarWidth($WidgetRatingStarsCurrentWidth);
    }

    public function getName()
    {
        return 'ratingStars';
    }

    public function render($image = null)
    {
        $this->setImage($image);
        return $this->createRatingStar()->getImage();
    }


    public function createRatingStar()
    {
        $opacity = 100;

        $stars = $this->getStars();

        $emptyStar = $stars['0'];
        $fullStar = $stars['1'];

        // Take the integer part of the rating(sum of fully stars).
        $StarSumFirstPart = $StarSum = (int)$this->rating;

        $WidgetRatingStarsPaddingLaft = $this->getPositionX();
        $this->countStarSizes($fullStar);

        if ($StarSumFirstPart != 0) {

            for ($i = 1; $i <= $StarSumFirstPart; $i++) {
                $WidgetRatingStarsCurrent = imageCreateFromGIF($fullStar);
                ImageAlphaBlending($WidgetRatingStarsCurrent, true);
                imageSaveAlpha($WidgetRatingStarsCurrent, true);

                imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getElementHeight(), $opacity);
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
                    $WidgetRatingStarsCurrentPath = $stars['025'];
                    break;
                case ($StarSumSecondPart >= 0.5 && $StarSumSecondPart < 0.75) :
                    $WidgetRatingStarsCurrentPath = $stars['05'];
                    break;
                case ($StarSumSecondPart >= 0.75 && $StarSumSecondPart < 1) :
                    $WidgetRatingStarsCurrentPath = $stars['075'];
                    break;
            }

            $WidgetRatingStarsCurrent = imageCreateFromGIF($WidgetRatingStarsCurrentPath);
            ImageAlphaBlending($WidgetRatingStarsCurrent, true);
            imageSaveAlpha($WidgetRatingStarsCurrent, true);

            imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getElementHeight(), $opacity);
            imageSaveAlpha($this->image, true);

            $WidgetRatingStarsPaddingLaft += $this->getStarWidth();
        }

        // Finish, empty star(s).
        if ($StarSum != 5) {
            for ($i = $StarSum; $i < 5; $i++) {
                $WidgetRatingStarsCurrent = imageCreateFromGIF($emptyStar);
                ImageAlphaBlending($WidgetRatingStarsCurrent, true);
                imageSaveAlpha($WidgetRatingStarsCurrent, true);

                imagecopymerge($this->image, $WidgetRatingStarsCurrent, $WidgetRatingStarsPaddingLaft, $this->getPositionY(), 0, 0, $this->getStarWidth(), $this->getElementHeight(), $opacity);
                imageSaveAlpha($this->image, true);
                $WidgetRatingStarsPaddingLaft += $this->getStarWidth();
            }
        }

        return $this;

    }

    /**
     * @param mixed $elementHeight
     */
    public function setElementHeight($elementHeight)
    {
        $this->elementHeight = $elementHeight;
    }

    /**
     * @return mixed
     */
    public function getElementHeight()
    {
        if ($this->elementHeight === null)
        {
            $stars = $this->getStars();
            $this->countStarSizes($stars['1']);
        }
        return $this->elementHeight;
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

    /**
     * @param mixed $rating
     * @return $this;
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return mixed
     */
    public function getStars()
    {
        return $this->resources['stars'];
    }


}