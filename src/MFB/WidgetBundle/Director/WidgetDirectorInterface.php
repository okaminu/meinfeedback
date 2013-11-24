<?php

namespace MFB\WidgetBundle\Director;
use MFB\WidgetBundle\Entity\Color;

interface WidgetDirectorInterface
{
    public function build(
        $lastFeedbacks,
        $feedbackCount,
        $feedbackRatingCount,
        $feedbackRatingAverage,
        Color $textColor,
        Color $feedbackColor
    );
}