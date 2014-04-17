<?php

namespace MFB\EmailBundle\Placeholder\Holders;

use MFB\EmailBundle\Placeholder\PlaceholderInterface;

class PlaceholderLink implements PlaceholderInterface
{
    protected $baseUrl = FOXRATE_SERVER;
    protected $accountId;
    protected $orderId;

    public function getName()
    {
        return 'link';
    }

    public function getValue()
    {
        return $this->baseUrl . 'order/' . $this->sf_login_id . '/' . $this->orderId;
    }

    /**
     * @param $accountId
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @param $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }
}
