<?php

namespace MFB\WidgetBundle\Builder;

use MFB\WidgetBundle\Builder\Elements\ElementInterface;

interface BuilderInterface
{
    public function getLayout();

    public function addElement(ElementInterface $block);
}

