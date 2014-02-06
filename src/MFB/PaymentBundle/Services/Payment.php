<?php
namespace MFB\PaymentBundle\Services;

use MFB\AccountBundle\Service\Account;
use MFB\ReskribeBundle\Service\Api as ReskribeApi;

class Payment
{
    private $reskribe;
    private $accountService;

    public function __construct(ReskribeApi $reskribe, Account $account)
    {
        $this->reskribe = $reskribe;
        $this->accountService = $account;
    }

    public function getSignUrl($accountId)
    {
        $account = $this->accountService->findByAccountId($accountId);
        return $this->reskribe->getSignUrl($account->getHash(), $account->getEmail(), $account->getName());
    }
}