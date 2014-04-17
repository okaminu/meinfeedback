<?php

namespace MFB\EmailBundle\Placeholder\Holders;

use MFB\EmailBundle\Placeholder\PlaceholderInterface;

class PlaceholderUnsubscribe implements PlaceholderInterface
{
    protected $baseUrl = FOXRATE_SERVER;
    protected $accountId;
    protected $serviceEmail;

    public function getName()
    {
        return 'unsubscribe';
    }

    public function getValue()
    {
        return (!empty($this->accountId)) ? $this->baseUrl .  'index.php?page=unsubscribe&email=' . $this->serviceEmail . '&login_id=' . $this->accountId : '';
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param mixed $serviceEmail
     */
    public function setServiceEmail($serviceEmail)
    {
        $this->serviceEmail = $serviceEmail;
        return $this;
    }



}
