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

    public function __construct(Request $request, SecurityContext $sc, Account $as)
    {
        $this->request = $request;
        $this->securityContext = $sc;
        $this->accountService = $as;
    }

    public function login($accountId, $area)
    {
        $account = $this->accountService->findByAccountId($accountId);
        $token = new UsernamePasswordToken($account, $account->getPassword(), $area);
        $this->securityContext->setToken($token);
        $this->request->getSession()->set('_security_secured_area', serialize($token));
    }
}
