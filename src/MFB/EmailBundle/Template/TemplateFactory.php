<?php


namespace MFB\EmailBundle\Template;

class TemplateFactory
{
    public function get($name)
    {
        $className = $name . 'Template';
        return new $className;
    }
}
