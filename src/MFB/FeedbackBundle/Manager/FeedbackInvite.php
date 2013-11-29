<?php
namespace MFB\FeedbackBundle\Manager;

use MFB\FeedbackBundle\Entity\FeedbackInvite as FeedbackInviteEntity;

/**
 * Class FeedbackInvite
 * @package MFB\FeedbackBundle\Manager
 * @deprecated We should leave symfony handle joins
 */
class FeedbackInvite
{
    private $accountId;

    private $channelId;

    private $customerId;

    private $invite;

    public function __construct(
        $accountId,
        $channelId,
        $customerId,
        FeedbackInviteEntity $feedbackInvite
    ) {
        $this->accountId = $accountId;
        $this->channelId = $channelId;
        $this->customerId = $customerId;
        $this->invite = $feedbackInvite;
    }

    public function createEntity()
    {
        $this->invite->setAccountId($this->accountId);
        $this->invite->setCustomerId($this->customerId);
        $this->invite->setChannelId($this->channelId);
        $this->invite->updatedTimestamps();
        return $this->invite;
    }
}
