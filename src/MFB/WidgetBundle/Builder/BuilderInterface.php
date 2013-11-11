<?php

namespace MFB\WidgetBundle\Builder;

interface BuilderInterface
{
    public function getLayout();

    public function addElement($block);
}

