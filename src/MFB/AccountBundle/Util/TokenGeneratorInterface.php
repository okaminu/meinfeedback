<?php


namespace MFB\AccountBundle\Util;


interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();

    public function generatePassword();
}
