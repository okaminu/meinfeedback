<?php
namespace MFB\AdminBundle\Service;

class FormSetupPaths
{
    private $paths;
    
    public function __construct($paths)
    {
        $this->paths = $paths;
    }

    public function getPaths()
    {
        return $this->paths;
    }

}
