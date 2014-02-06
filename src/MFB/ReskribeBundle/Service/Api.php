<?php

namespace MFB\ReskribeBundle\Service;


use Buzz\Browser;
use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;

class Api
{
    protected $em;
    protected $buzz;
    protected $reskribeToken;

    public function __construct(EntityManager $em, Browser $buzz, $reskribeToken)
    {
        $this->em = $em;
        $this->buzz = $buzz;
        $this->reskribeToken = $reskribeToken;
    }

    public function getSignUrl($accountId, $accountEmail, $accountName)
    {
        $data['plan_code'] = 'b01';
        $data['uid'] = $accountId;
        $data['subscription[email]'] = $accountEmail;
        if ($name = $accountName) {
            $separated = array_filter(explode(' ', $name));
            if (count($separated) > 1) {
                $data['subscription[lastname]'] = array_pop($separated);
                $data['subscription[firstname]'] = implode(' ', $separated);
            } else {
                $data['subscription[firstname]'] = $name;
            }
        }
        $data['api_token'] = $this->reskribeToken;

        $result = $this->buzz->submit(
            'https://api.reskribe.com/v1/forms',
            $data
        );
        $resultData = json_decode($result->getContent());

        return $resultData->url;

    }
}