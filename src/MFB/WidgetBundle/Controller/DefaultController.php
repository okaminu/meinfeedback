<?php

namespace MFB\WidgetBundle\Controller;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\WidgetBundle\Generator\ImageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($accountId)
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

        $lastFeedbacks = $em->getRepository('MFBFeedbackBundle:Feedback')->findBy(
            array(
                'accountId' => $account->getId(),
                'channelId' => $accountChannel->getId()
            ),
            array('id'=>'DESC'),
            4
        );

        $query = $em->createQuery('SELECT COUNT(fb.id) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1');
        $query->setParameter(1, $accountChannel->getId());
        $feedbackCount = $query->getSingleScalarResult();

        $query = $em->createQuery('SELECT COUNT(fb.id) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1 AND fb.rating IS NOT NULL');
        $query->setParameter(1, $accountChannel->getId());
        $feedbackRatingCount = $query->getSingleScalarResult();

        $query = $em->createQuery('SELECT AVG(fb.rating) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1');
        $query->setParameter(1, $accountChannel->getId());
        $feedbackRatingAverage = round($query->getSingleScalarResult(), 1);

        $imageBuilder = new ImageBuilder();
        $imageBuilder->setResources($this->getResources());
        $imageBlob = $imageBuilder->build($lastFeedbacks, $feedbackCount, $feedbackRatingCount, $feedbackRatingAverage);

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($imageBlob);
        return $response;
    }

    protected function getResources()
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


}
