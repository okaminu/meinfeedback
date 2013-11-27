<?php


namespace MFB\AccountBundle\Util;


use MFB\AccountBundle\Entity\Account;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;

class Encoder
{

    private $encoderFactory;

    private $secure_random;

    public function __construct(EncoderFactoryInterface $encoderFactory, SecureRandomInterface $secure_random)
    {
        $this->encoderFactory = $encoderFactory;

        $this->secure_random = $secure_random;
    }

    public function encodePassword(Account $account)
    {
        if (0 !== strlen($password = $account->getPlainPassword())) {
            /** @var EncoderFactoryInterface $encoder */
            $encoder = $this->getEncoderFactory()->getEncoder($account);
            $account->setSalt(base64_encode($this->getSecureRandom()->nextBytes(20)));
            $account->setPassword($encoder->encodePassword($password, $account->getSalt()));
        }
    }

    /**
     * @param mixed $encoderFactory
     */
    public function setEncoderFactory($encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @return mixed
     */
    public function getEncoderFactory()
    {
        return $this->encoderFactory;
    }

    /**
     * @param mixed $secure_random
     */
    public function setSecureRandom($secure_random)
    {
        $this->secure_random = $secure_random;
    }

    /**
     * @return mixed
     */
    public function getSecureRandom()
    {
        return $this->secure_random;
    }
}
