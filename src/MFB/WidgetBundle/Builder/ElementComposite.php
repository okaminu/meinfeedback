<?php

namespace MFB\WidgetBundle\Builder;

class ElementComposite implements Elements\ElementInterface
{
    /**
     * @var array
     */
    private $elements = array();

    public function add(Elements\ElementInterface $element)
    {
        $this->elements[] = $element;
    }

    public function getName()
    {
        $names = array();
        /**@param Elements\ElementInterface $element */
        foreach ($this->elements as $element) {
            $names[] = $element->getName();
        }
        return implode('_', $names);
    }

    public function render()
    {
        $image = null;
        foreach ($this->elements as $element) {
            $image = $element->render($image);
        }
        return $image;
    }

}
