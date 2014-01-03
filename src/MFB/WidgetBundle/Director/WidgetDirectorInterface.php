<?php

namespace MFB\WidgetBundle\Director;
use MFB\WidgetBundle\Entity\Color;

interface WidgetDirectorInterface
{
    public function build(
        $lastFeedbacks,
        $feedbackCount,
        $feedbackRatingAverage,
        Color $textColor,
        Color $feedbackColor
    );
}