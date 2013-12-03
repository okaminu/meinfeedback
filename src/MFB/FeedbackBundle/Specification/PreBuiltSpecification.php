<?php
namespace MFB\FeedbackBundle\Specification;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel as Channel;

/**
 * Gives most commonly used specifications
 * Class PreBuiltSpecification
 * @package MFB\FeedbackBundle\Specification
 */
class PreBuiltSpecification
{
    /**
     * @var \MFB\AccountBundle\Entity\Account
     */
    protected $account;

    /**
     * @var \MFB\ChannelBundle\Entity\AccountChannel
     */
    protected $accountChannel;

    public function __construct(Account $account, Channel $accountChannel)
    {
        $this->account = $account;
        $this->accountChannel = $accountChannel;
    }

    /**
     * @return AndX
     */
    public function getFeedbackSpecification()
    {
        return new AndX(
            new FilterAccountId($this->account->getId()),
            new FilterChannelId($this->accountChannel->getId()),
            new FilterIsEnabled()
        );
    }

    /**
     * @return AndX
     */
    public function getFeedbackWithRatingSpecification()
    {
        return new AndX(
            new FilterAccountId($this->account->getId()),
            new FilterChannelId($this->accountChannel->getId()),
            new FilterIsEnabled(),
            new FilterWithRating()
        );
    }

}