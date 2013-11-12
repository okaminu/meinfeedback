<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\DBALException;

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

        return $this->showFeedbackForm($account->getId(), $accountChannel, $form->createView());
    }

    public function saveAction(Request $request)
    {
        $rating = null;
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

        $customer = new Customer();
        $customer->setAccountId($account->getId());
        $form = $this->createForm(new CustomerType(), $customer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($customer);
                $em->flush();

                $feedback = new Feedback();
                $feedback->setAccountId($account->getId());
                $feedback->setChannelId($accountChannel->getId());
                $feedback->setCustomerId($customer->getId());
                $feedback->setContent($request->get('feedback'));

                $requestRating = (int)$request->get('rating');

                if (($requestRating > 0) && ($requestRating <= 5)) {
                    $rating = $requestRating;
                }

                if (($accountChannel->getRatingsEnabled() == '1') && (is_null($rating))) {
                    $this->showFeedbackForm(
                        $account->getId(),
                        $accountChannel,
                        $form->createView(),
                        $request->get('feedback'),
                        'Please select star rating'
                    );
                }

                $feedback->setRating($rating);
                $em->persist($feedback);
                $em->flush();

            } catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                    $form->get('email')->addError(new FormError('Email already exists'));
                } else {
                    $form->addError(new FormError($ex->getMessage()));
                }
            }
            return $this->render('MFBFeedbackBundle:Invite:thank_you.html.twig');
        }
        return $this->showFeedbackForm($account->getId(), $accountChannel, $form->createView());
    }


    private function showFeedbackForm($accountId, $accountChannel, $formView, $feedback = '', $starErrorMessage = false)
    {
        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountId' => $accountId,
                'accountChannel' => $accountChannel,
                'starErrorMessage' => $starErrorMessage,
                'feedback' => $feedback,
                'form' => $formView
            )
        );
    }
}
