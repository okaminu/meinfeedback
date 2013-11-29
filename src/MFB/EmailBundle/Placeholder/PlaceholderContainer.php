<?php


namespace MFB\EmailBundle\Placeholder;

use MFB\EmailBundle\Placeholder\Holders\PlaceholderGeneric;

class PlaceholderContainer
{
    protected $placeholders = array();

    /**
     * @param $placeholders
     */
    public function setPlaceholders($placeholders)
    {
        $this->placeholders[$placeholders->getName()] = $placeholders;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getPlaceholder($name)
    {
        return $this->placeholders[$name];
    }

    /**
     *
     */
    public function getTranslation($translate)
    {
        foreach ($this->placeholders as $placeholder) {
            $translate = str_replace($this->makeBBCode($placeholder->getName()), $placeholder->getValue(), $translate);
        }

        return $translate;
    }


    public function makeBBCode($key)
    {
        return '#' . strtoupper($key) . '#';
    }

    /**
     * Get BB code values
     *
     * @return array
     */
    public function getBBcodeValues()
    {
        $tr = array();
        foreach ($this->placeholders as $placeholder) {
            $tr[$this->makeBBCode($placeholder->getName())] = $placeholder->getValue();
        }

        return $tr;
    }

    public static function getPreparedContainer()
    {
        $cont = new self();
        $cont->setPlaceholders(new PlaceholderGeneric('firstname'));
        $cont->setPlaceholders(new PlaceholderGeneric('lastname'));
        $cont->setPlaceholders(new PlaceholderGeneric('homepage_url'));
        $cont->setPlaceholders(new PlaceholderGeneric('gender'));
        $cont->setPlaceholders(new PlaceholderGeneric('sal'));
        $cont->setPlaceholders(new PlaceholderGeneric('email'));

        return $cont;
    }
}