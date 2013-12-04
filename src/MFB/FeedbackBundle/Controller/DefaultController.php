<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\ServiceBundle\Entity\Service;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    public function indexAction($accountId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Account $account */
        $account = $em->find('MFBAccountBundle:Account', $accountId);
        if (!$account) {
            throw $this->createNotFoundException('Account does not exist');
        }

        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $account->getId())
        );

        if (!$accountChannel) {
            throw $this->createNotFoundException('Account does not have any channels');
        }

        $feedback = new Feedback();
        $feedback->setChannelId($accountChannel->getId());
        $feedback->setAccountId($account->getId());

        $customer = new Customer();
        $customer->setAccountId($account->getId());

        $service = new Service();
        $service->setAccountId($account->getId());
        $service->setChannelId($accountChannel->getId());

        $customer->addService($service);
        $service->setCustomer($customer);
        $feedback->setCustomer($customer);
        $form = $this->getFeedbackForm($feedback);

        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'form' => $form->createView(),
                'accountChannel' => $accountChannel
            )
        );
    }

    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->container->get('event_dispatcher');
        $errorMessage = null;
        $dispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);

        /** @var Account $account */
        $account = $this->get("mfb_account.manager")->findAccountByAccountId($request->get('accountId'));
        /** @var AccountChannel $accountChannel */
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($account);

        $feedback = new Feedback();
        $feedback->setChannelId($accountChannel->getId());
        $feedback->setAccountId($account->getId());

        $customer = new Customer();
        $customer->setAccountId($account->getId());

        $service = new Service();
        $service->setAccountId($account->getId());
        $service->setChannelId($accountChannel->getId());

        $customer->addService($service);
        $service->setCustomer($customer);
        $feedback->setCustomer($customer);
        $form = $this->getFeedbackForm($feedback);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($feedback);
                $em->flush();

                $event = new CustomerAccountEvent($feedback->getId(), $account, $customer, $request);
                $dispatcher->dispatch(FeedbackEvents::REGULAR_COMPLETE, $event);

                $return_url = $this->getReturnUrl($accountChannel);

                return $this->render(
                    'MFBFeedbackBundle:Invite:thank_you.html.twig',
                    array(
                        'thankyou_text' => $this->get('mfb_email.template')->getText($customer, 'ThankYou'),
                        'homepage' => $return_url
                    )
                );

            } catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                    $form->get('customer')->get('email')->addError(new FormError('Email already exists'));
                } else {
                    $form->addError(new FormError($ex->getMessage()));
                }
            }
        }

        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'form' => $form->createView(),
                'accountChannel' => $accountChannel
            )
        );
    }

    /**
     * @param $accountChannel
     * @return string
     */
    protected function getReturnUrl($accountChannel)
    {
        $return_url = $this->generateUrl(
            'mfb_account_profile_homepage',
            array('accountId' => $accountChannel->getAccountId()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        if ($accountChannel->getHomepageUrl()) {
            $return_url = $accountChannel->getHomepageUrl();
        }
        return $return_url;
    }

    protected function getFeedbackEnableLink($id)
    {
        return $url = $this->generateUrl(
            'mfb_feedback_enable',
            array('feedback_id' => $id)
        );
    }

    /**
     * @param $feedback
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm(Feedback $feedback)
    {
        $form = $this->createForm(new FeedbackType(), $feedback, array(
            'action' => $this->generateUrl('mfb_feedback_save', array(
                        'accountId' => $feedback->getAccountId(),
                        'accountChannelId' => $feedback->getChannelId()
                    )),
                'method' => 'POST',
            ));
        return $form;
    }
}
