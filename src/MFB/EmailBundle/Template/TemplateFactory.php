<?php

namespace MFB\EmailBundle\Template;

class TemplateFactory
{
    public function get($name)
    {
        $className = 'MFB\EmailBundle\Template\\' . $name . 'Template';
        return new $className;
    }
}
