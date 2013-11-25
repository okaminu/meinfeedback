<?php


namespace MFB\WidgetBundle\Service;

use MFB\WidgetBundle\Director\MainWidgetDirector;
use MFB\FeedbackBundle\Specification as Spec;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use Doctrine\ORM\EntityManager;
use MFB\WidgetBundle\Builder\BuilderInterface;
use MFB\WidgetBundle\Entity\Color;

/**
 * Class Widget
 *
 * @package MFB\WidgetBundle\Service
 */
class Widget
{

    protected $em;

    protected $imageBuilder;

    public function __construct(EntityManager $em, BuilderInterface $imageBuilder)
    {
        $this->em = $em;

        $this->imageBuilder = $imageBuilder;
    }

    /**
     * Create Widget Image
     *
     * @param $accountId
     * @return mixed
     */
    public function createMainWidget($accountId)
    {
        /** @var EntityManager $em */
        $em = $this->em;
        /** @var Account $account */
        $account = $em->getRepository('MFBAccountBundle:Account')->findAccountByAccountId($accountId);
        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findAccountChannelByAccount($account);

        $widget = $em->getRepository('MFBWidgetBundle:Widget')->findOneBy(
            array('accountId' => $account->getId(), 'channelId' => $accountChannel->getId())
        );

        $specification = $this->feedbackSpecification($account, $accountChannel);
        $lastFeedbacks  = $this->getFeedbackRepo()->findSortedFeedbacks($specification);
        $feedbackCount = $this->getFeedbackRepo()->getFeedbackCount($specification);

        $withRatingsSpecification = $this->getFeedbackWithRatingSpecification($account, $accountChannel);
        $feedbackRatingCount = $this->getFeedbackRepo()->getFeedbackCount($withRatingsSpecification);
        $feedbackRatingAverage = round($this->getFeedbackRepo()->getRatingsAverage($withRatingsSpecification), 1);

        $imageDirector = new MainWidgetDirector($this->imageBuilder);
        return $imageDirector->build(
            $lastFeedbacks,
            $feedbackCount,
            $feedbackRatingCount,
            $feedbackRatingAverage,
            new Color($widget->getTextColorCode()),
            new Color($widget->getBackgroundColorCode())
        );
    }

    /**
     * Get Feedback repository
     * @return \MFB\FeedbackBundle\Repository\FeedbackRepository
     */
    protected function getFeedbackRepo()
    {
        return $this->em->getRepository('MFBFeedbackBundle:Feedback');
    }

    /**
     * @param $account
     * @param $accountChannel
     * @return Spec\AndX
     */
    public function feedbackSpecification($account, $accountChannel)
    {
        $specification = new Spec\AndX(
            new Spec\FilterAccountId($account->getId()),
            new Spec\FilterChannelId($accountChannel->getId()),
            new Spec\FilterIsEnabled()
        );
        return $specification;
    }

    /**
     * @param $account
     * @param $accountChannel
     * @return Spec\AndX
     */
    public function getFeedbackWithRatingSpecification($account, $accountChannel)
    {
        $withRatingsSpecification = new Spec\AndX(
            new Spec\FilterAccountId($account->getId()),
            new Spec\FilterChannelId($accountChannel->getId()),
            new Spec\FilterIsEnabled(),
            new Spec\FilterWithRating()
        );
        return $withRatingsSpecification;
    }

} 