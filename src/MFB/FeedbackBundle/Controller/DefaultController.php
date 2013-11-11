<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;

class DefaultController extends Controller
{
    public function indexAction($accountId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Customer();
        $entity->setAccountId($accountId);
        $form = $this->createForm(new CustomerType(), $entity);

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exist');
        }

        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$account->getId())
        );
        if (!$accountChannel) {
            throw $this->createNotFoundException('Account does not have any channels');
        }

        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountId' => $account->getId(),
                'accountChannelId' => $accountChannel->getId(),
                'account_channel_name' => $accountChannel->getName(),
                'ratingEnabled' => $accountChannel->getRatingsEnabled(),
                'errorMessage' => false,
                'feedback' => '',
                'form' => $form->createView()
            )
        );
    }

    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $request->get('accountId'));
        if (!$account) {
            throw $this->createNotFoundException('Account does not exist');
        }

        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->find('MFBChannelBundle:AccountChannel', $request->get('accountChannelId'));
        if (!$accountChannel) {
            throw $this->createNotFoundException('Channel does not exist');
        }

        //customer Id

        $entity = new Customer();
        $entity->setAccountId($account);
        $form = $this->createForm(new CustomerType(), $entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try
            {
                $em->persist($entity);
                $em->flush();
                $customerId = $entity->getId();




        $feedback = new Feedback();
        $feedback->setAccountId($account);
        $feedback->setChannelId($accountChannel);
        $feedback->setCustomerId($customerId);
        $feedback->setContent($request->get('feedback'));

        $rating = null;

        $requestRating = (int)$request->get('rating');

        if (($requestRating > 0) && ($requestRating <= 5)) {
            $rating = $requestRating;
        }

        if (($accountChannel->getRatingsEnabled() == '1') && (is_null($rating))) {

            return $this->render(
                'MFBFeedbackBundle:Default:index.html.twig',
                array(
                    array(
                        'accountId' => $account->getId(),
                        'accountChannelId' => $accountChannel->getId(),
                        'account_channel_name' => $accountChannel->getName(),
                        'ratingEnabled' => $accountChannel->getRatingsEnabled(),
                        'errorMessage' => 'Please select star rating',
                        'feedback' => $request->get('feedback'),
                        'form' => $form->createView()
                        )
                    )
            );
        }

        $feedback->setRating($rating);
        $em->persist($feedback);
        $em->flush();

            }
            catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000 ) {
                    $form->get('email')->addError( new FormError('Email already exists'));
                } else {
                    $form->addError( new FormError($ex->getMessage()));
                }

            }
        }
        return $this->render('MFBFeedbackBundle:Invite:thank_you.html.twig');
    }
}
