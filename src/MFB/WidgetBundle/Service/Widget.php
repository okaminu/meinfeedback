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
        $accountChannel = $this->container->get("mfb_account_channel.service")->findByAccountId(
            $account->getId()
        );

        $widget = $em->getRepository('MFBWidgetBundle:Widget')->findOneBy(
            array('accountId' => $account->getId(), 'channelId' => $accountChannel->getId())
        );

        $feedbackService = $this->container->get('mfb_feedback_display.service');

        $lastFeedbacks = $feedbackService->getActiveFeedbackSummaryList($accountChannel->getId());
        $this->filterComments($lastFeedbacks);

        $imageDirector = new MainWidgetDirector($this->imageBuilder);
        return $imageDirector->build(
            $lastFeedbacks,
            $feedbackService->getChannelFeedbackCount($accountChannel->getId()),
            $feedbackService->getChannelRatingAverage($accountChannel->getId()),
            new Color($widget->getTextColorCode()),
            new Color($widget->getBackgroundColorCode())
        );
    }

    /**
     * @param $lastFeedbacks
     */
    private function filterComments($lastFeedbacks)
    {
        foreach ($lastFeedbacks as $feedbackSummary) {
            $feedback = $feedbackSummary->getFeedback();
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