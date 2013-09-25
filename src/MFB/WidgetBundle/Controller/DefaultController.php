<?php

namespace MFB\WidgetBundle\Controller;

use Doctrine\ORM\EntityManager;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\FeedbackBundle\Entity\Feedback;
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
        /** @var Feedback $lastFeedback */
        $lastFeedback = reset($lastFeedbacks);


        $query = $em->createQuery('SELECT COUNT(fb.id) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1');
        $query->setParameter(1, $accountChannel->getId());
        $feedbackCount = $query->getSingleScalarResult();

        $widgetTemplate = $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/widgets/n1.png');
        $arialFontFile = $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Arial_Bold.ttf');
        $lucidaFontFile = $this->get('kernel')->locateResource('@MFBWidgetBundle/Resources/fonts/Lucida_Grande.ttf');

        $img = imagecreatefrompng($widgetTemplate);

        $comment = '';
        foreach ($lastFeedbacks as $feedback) {
            try {
                $comment = $this->wrap(
                    9,
                    $lucidaFontFile,
                    $comment . '"'.$feedback->getContent().'"'."\n\n",
                    170,
                    170
                );
            } catch (\Exception $ex) {

            }
        }
        if ($comment == '') {
            $comment = $this->wrap(
                9,
                $lucidaFontFile,
                '"'. substr($lastFeedback->getContent(), 0, 500).'.."',
                170,
                170
            );

        }

        $fontColorTop =imagecolorallocate($img, 230, 230, 230);
        $fontColorBottom =imagecolorallocate($img, 108, 108, 108);

        imagettftext(
            $img, //img to apply
            8, // size
            0, // angle
            120, // x
            222, // y
            $fontColorBottom, // color
            $arialFontFile, // font file
            $lastFeedback->getCreatedAt()->format('d.m.Y') // text
        );

        imagettftext(
            $img, //img to apply
            8, // size
            0, // angle
            10, // x
            222, // y
            $fontColorBottom, // color
            $arialFontFile, // font file
            $feedbackCount.' Bewertungen' // text
        );

        imagettftext(
            $img, //img to apply
            9, // size
            0, // angle
            10, // x
            40, // y
            $fontColorTop, // color
            $lucidaFontFile, // font file
            $comment // text
        );


        ob_start();
        imagepng($img);
        $imageBlob = ob_get_contents();
        ob_end_clean();
        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($imageBlob);
        return $response;
    }

    protected function wrap($fontSize, $fontFace, $string, $width, $maxHeight)
    {

        $ret = "";
        $arr = explode(" ", $string);

        foreach ($arr as $word) {
            $testboxWord = imagettfbbox($fontSize, 0, $fontFace, $word);

            // huge word larger than $width, we need to cut it internally until it fits the width
            $len = strlen($word);
            while ($testboxWord[2] > $width && $len > 0) {
                $word = substr($word, 0, $len);
                $len--;
                $testboxWord = imagettfbbox($fontSize, 0, $fontFace, $word);
            }

            $teststring = $ret.' '.$word;
            $testboxString = imagettfbbox($fontSize, 0, $fontFace, $teststring);
            if ($testboxString[2] > $width) {
                $ret.=($ret==""?"":"\n").$word;
            } else {
                $ret.=($ret==""?"":' ').$word;
            }
            if ($testboxString[3] > $maxHeight) {
                throw new \Exception('too large text');
            }
        }

        return $ret;
    }
}
