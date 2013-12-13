<?php


namespace MFB\WidgetBundle\Service;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Specification as Spec;
use MFB\WidgetBundle\Builder\BuilderInterface;
use MFB\WidgetBundle\Director\MainWidgetDirector;
use MFB\WidgetBundle\Entity\Color;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MFB\FeedbackBundle\Specification\PreBuiltSpecification;

/**
 * Class Widget
 *
 * @package MFB\WidgetBundle\Service
 */
class Widget
{

    protected $em;

    protected $imageBuilder;

    protected $params;

    public function __construct(
        EntityManager $em,
        BuilderInterface $imageBuilder,
        ContainerInterface $container,
        $params
    ) {
        $this->em = $em;
        $this->imageBuilder = $imageBuilder;
        $this->container = $container;
        $this->params = $params;
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
        $account = $this->container->get("mfb_account.service")->findByAccountId($accountId);
        /** @var AccountChannel $accountChannel */
        $accountChannel = $this->container->get("mfb_account_channel.manager")->findAccountChannelByAccount(
            $account->getId()
        );

        $widget = $em->getRepository('MFBWidgetBundle:Widget')->findOneBy(
            array('accountId' => $account->getId(), 'channelId' => $accountChannel->getId())
        );

        $prebuiltSpec = new PreBuiltSpecification($account, $accountChannel);
        $specification = $prebuiltSpec->getFeedbackSpecification();
        $lastFeedbacks  = $this->getFeedbackRepo()->findSortedFeedbacks($specification);
        $feedbackCount = $this->getFeedbackRepo()->getFeedbackCount($specification);

        $withRatingsSpecification = $prebuiltSpec->getFeedbackWithRatingSpecification();
        $feedbackRatingCount = $this->getFeedbackRepo()->getFeedbackCount($withRatingsSpecification);
        $feedbackRatingAverage = round($this->getFeedbackRepo()->getRatingsAverage($withRatingsSpecification), 1);

        $this->filterComments($lastFeedbacks);

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
     * @param $lastFeedbacks
     */
    private function filterComments($lastFeedbacks)
    {
        foreach ($lastFeedbacks as $feedback) {
            $filteredText = $feedback->getContent();
            $textArray = explode(' ', $feedback->getContent());

            if (count($textArray) > $this->params['widgetWordCount']) {
                $filteredTextArray = array_slice($textArray, 0, $this->params['widgetWordCount']);
                $filteredTextArray[] = $this->params['lastWordEnding'];
                $filteredText = implode(' ', $filteredTextArray);
            }
            $feedback->setContent($filteredText);
        }
    }

}