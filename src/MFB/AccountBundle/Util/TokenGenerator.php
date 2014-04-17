<?php

namespace MFB\AccountBundle\Util;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    private $logger;
    private $useOpenSsl;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        // determine whether to use OpenSSL
        if (defined('PHP_WINDOWS_VERSION_BUILD') && version_compare(PHP_VERSION, '5.3.4', '<')) {
            $this->useOpenSsl = false;
        } elseif (!function_exists('openssl_random_pseudo_bytes')) {
            if (null !== $this->logger) {
                $this->logger
                    ->notice('It is recommended that you enable the "openssl" extension for random number generation.');
            }
            $this->useOpenSsl = false;
        } else {
            $this->useOpenSsl = true;
        }
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }

    private function getRandomNumber()
    {
        $nbBytes = 32;

        // try OpenSSL
        if ($this->useOpenSsl) {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

            if (false !== $bytes && true === $strong) {
                return $bytes;
            }

            if (null !== $this->logger) {
                $this->logger->info('OpenSSL did not produce a secure random number.');
            }
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }

    /**
     * Generate readable password
     *
     * @param int $len
     * @return string
     */
    public function generatePassword($len = 8)
    {
        if (($len%2)!==0) {
            $len=8;
        }

        $length=$len-2;
        $conso=array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','x','y','z');
        $vocal=array('a','e','i','o','u');
        $password='';
        srand((double)microtime()*1000000);

        $max = $length/2;
        for ($i=1; $i<=$max; $i++) {
            $password.=$conso[rand(0, 19)];
            $password.=$vocal[rand(0, 4)];
        }

        $password.=rand(10, 99);
        return $password;
    }
}
