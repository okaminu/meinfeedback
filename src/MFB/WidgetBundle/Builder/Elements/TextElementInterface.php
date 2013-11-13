<?php

namespace MFB\WidgetBundle\Builder\Elements;

interface TextElementInterface {

    /**
     * Get baseline modifier - a int to modify y coordinates by font size
     * @return int
     */
    public function getBaselineModifier();

    /**
     * Get text element font size
     * @return mixed
     */
    public function getFontSize();

}