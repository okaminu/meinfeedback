<?php

namespace MFB\AccountBundle\Service;

use MFB\AccountBundle\Entity\Account as AccountEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;

class AccountSecurity
{
    private $request;

    private $securityContext;
    
    private $accountService;

    private $encoder;

    private $random;

    public function __construct(Request $request, SecurityContext $sc, Account $as, $encoder, $random)
    {
        $this->request = $request;
        $this->securityContext = $sc;
        $this->accountService = $as;
        $this->encoder = $encoder;
        $this->random = $random;
    }

    public function login($accountId, $area)
    {
        $account = $this->accountService->findByAccountId($accountId);
        $this->loginAccount($area, $account);
    }

    public function loginByHash($hash, $area)
    {
        $account = $this->accountService->findByHash($hash);
        $this->loginAccount($area, $account);
    }


    public function encryptAccountPassword($account)
    {
        $encoder = $this->encoder->getEncoder($account);
        $account->setSalt(base64_encode($this->random->nextBytes(20)));
        $account->setPassword($encoder->encodePassword($account->getPassword(), $account->getSalt()));
        return $account;
    }

    public function generateRandomHash()
    {
        return sha1(uniqid(mt_rand(), true));
    }

    private function loginAccount($area, $account)
    {
        $token = new UsernamePasswordToken($account, $account->getPassword(), $area);
        $this->securityContext->setToken($token);
        $this->request->getSession()->set('_security_secured_area', serialize($token));
    }

}
