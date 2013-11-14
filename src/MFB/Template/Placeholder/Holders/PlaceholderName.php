<?php

namespace MFB\Template\Placeholder\Holders;

use MFB\Template\Placeholder\PlaceholderInterface;

class PlaceholderName implements PlaceholderInterface
{

    protected $userData;

    public function getName()
    {
        return 'name';
    }

    public function getValue()
    {
        $firstname = $this->getUserDataValue('owner_firstname');
        $lastname= $this->getUserDataValue('owner_lastname');
        return (!empty($firstname) || !empty($lastname)) ? $firstname . ' ' . $lastname : '';
    }

    /**
     * @param $userData
     * @return $this
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
        return $this;
    }

    /**
     * @param $key
     * @return string
     */
    public function getUserDataValue($key) //todiscuss
    {
        return $this->userData->getName();
    }
}
