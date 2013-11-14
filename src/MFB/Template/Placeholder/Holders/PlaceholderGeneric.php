<?php

namespace MFB\Template\Placeholder\Holders;

use MFB\Template\Placeholder\PlaceholderInterface;

class PlaceholderGeneric implements PlaceholderInterface
{
    protected $name;
    protected $userDataValue;

    public function __construct($name)
    {
        $this->setName($name);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return $this->getUserDataValue();
    }

    /**
     * @param $userDataValue
     * @return $this
     */
    public function setUserDataValue($userDataValue)
    {
        $this->userDataValue = $userDataValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserDataValue()
    {

        $emptyValue = '';

        return (!empty($this->userDataValue)) ? $this->userDataValue : $emptyValue;
    }



}
