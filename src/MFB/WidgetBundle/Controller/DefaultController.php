<?php

namespace MFB\WidgetBundle\Controller;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\WidgetBundle\Builder\ImageBuilder;
use MFB\WidgetBundle\Director\MainWidgetDirector;
use MFB\WidgetBundle\Entity\Color;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MFB\FeedbackBundle\Specification as Spec;
use MFB\WidgetBundle\Entity\Widget as WidgetEntity;

class DefaultController extends Controller
{
    public function indexAction($accountId)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($this->widgetImage($accountId));
        return $response;
    }

    protected function widgetImage($accountId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exits');
        }
        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$account->getId())
        );
        if (!$accountChannel) {
            throw $this->createNotFoundException('No feedback yet. Sorry.');
        }

        $widget = $em->getRepository('MFBWidgetBundle:Widget')->findOneBy(
            array('accountId' => $account->getId(), 'channelId' => $accountChannel->getId())
        );
        if (!$widget) {
            $widget = new WidgetEntity();
            $widget->setAccountId($account->getId());
            $widget->setChannelId($accountChannel->getId());
            $widget->setTextColorCode('6c6c6c');
            $widget->setBackgroundColorCode('ff0000');
            $em->persist($widget);
            $em->flush();
        }


        $specification = new Spec\AndX(
            new Spec\FilterAccountId($account->getId()),
            new Spec\FilterChannelId($accountChannel->getId()),
            new Spec\FilterIsEnabled()
        );
        $lastFeedbacks  = $this->getFeedbackRepo()->findSortedFeedbacks($specification);
        $feedbackCount = $this->getFeedbackRepo()->getFeedbackCount($specification);

        $withRatingsSpecification = new Spec\AndX(
            new Spec\FilterAccountId($account->getId()),
            new Spec\FilterChannelId($accountChannel->getId()),
            new Spec\FilterIsEnabled(),
            new Spec\FilterWithRating()
        );
        $feedbackRatingCount = $this->getFeedbackRepo()->getFeedbackCount($withRatingsSpecification);
        $feedbackRatingAverage = round($this->getFeedbackRepo()->getRatingsAverage($withRatingsSpecification), 1);

        $imageBuilder = new ImageBuilder($this->resources());
        $imageDirector = new MainWidgetDirector($imageBuilder);
        return $imageDirector->build(
            $lastFeedbacks,
            $feedbackCount,
            $feedbackRatingCount,
            $feedbackRatingAverage,
            new Color($widget->getTextColorCode()),
            new Color($widget->getBackgroundColorCode())
        );

    }

    protected function resources()
    {
        return array(
            'widgetTemplate' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/widgets/n1.png'),
            'arialFontFile' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Arial_Bold.ttf'),
            'lucidaFontFile' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Lucida_Grande.ttf'),
            'stars' => array(
                '0' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_0.gif'),
                '025' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_025.gif'),
                '05' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_05.gif'),
                '075' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_075.gif'),
                '1' => $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/public/images/stars/star_1.gif'),
            )
        );
    }


    protected function getFeedbackRepo()
    {
        return $this->getDoctrine()->getManager()->getRepository('MFBFeedbackBundle:Feedback');
    }

}
