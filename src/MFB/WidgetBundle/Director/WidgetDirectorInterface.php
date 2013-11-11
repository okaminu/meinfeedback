<?php

namespace MFB\WidgetBundle\Director;

interface WidgetDirectorInterface
{
    public function build($lastFeedbacks, $feedbackCount, $feedbackRatingCount, $feedbackRatingAverage);
}