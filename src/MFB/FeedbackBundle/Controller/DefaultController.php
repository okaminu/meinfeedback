<?php

namespace MFB\FeedbackBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\ServiceBundle\Entity\Service;

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
        $feedback->setChannelId($account->getId());
        $feedback->setAccountId($accountChannel->getId());

        $customer = new Customer();
        $customer->setAccountId($account->getId());


        $service = new Service();
        $service->setAccountId($account->getId());
        $service->setChannelId($accountChannel->getId());

        $form = $this->getFeedbackForm($customer, $service, $feedback);

        return $this->showFeedbackForm($account->getId(), $accountChannel, $form->createView());
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
        $feedback->setChannelId($account->getId());
        $feedback->setAccountId($accountChannel->getId());

        $customer = new Customer();
        $customer->setAccountId($account->getId());

        $service = new Service();
        $service->setAccountId($account->getId());
        $service->setChannelId($accountChannel->getId());

        $form = $this->getFeedbackForm($customer, $service, $feedback);

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

            } catch (\Exception $ex) {
                $errorMessage = 'Email already exists';
            }
            return $this->showFeedbackForm(
                $account->getId(),
                $accountChannel,
                $form->createView(),
                $request->get('feedback'),
                $errorMessage
            );
        }
        return $this->render('MFBFeedbackBundle:Default:invalid_data.html.twig');
    }

    private function showFeedbackForm($accountId, $accountChannel, $formView, $feedback = '', $errorMessage = false)
    {
        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountId' => $accountId,
                'accountChannel' => $accountChannel,
                'errorMessage' => $errorMessage,
                'feedback' => $feedback,
                'form' => $formView
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
     * @param $customer
     * @param $service
     * @param $feedback
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm(Customer $customer, Service $service, Feedback $feedback)
    {
        $customer->addService($service);
        $service->setCustomer($customer);
        $feedback->setCustomer($customer);
        $form = $this->createForm(new FeedbackType(), $feedback);
        return $form;
    }
}
