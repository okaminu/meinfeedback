<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\FeedbackBundle\Entity\Feedback;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\ServiceBundle\Form\ServiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\ServiceBundle\Entity\Service;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    public function showCreateFeedbackFormAction($accountId)
    {
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($accountId);
        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId);
        $form = $this->getFeedbackForm($feedback, $accountId, $accountChannel->getId());

        return $this->render(
            'MFBFeedbackBundle:Default:index.html.twig',
            array(
                'accountChannel' => $accountChannel,
                'form' => $form->createView()
            )
        );
    }

    public function saveFeedbackAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->container->get('event_dispatcher');
        $errorMessage = null;
        $dispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);

        /** @var Account $account */
        $account = $this->get("mfb_account.service")->findByAccountId($request->get('accountId'));
        /** @var AccountChannel $accountChannel */
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($account->getId());

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
        $form = $this->getFeedbackForm($feedback, null, null);

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

    public function showCreateFeedbackFormActionCP()
    {
        $accountId = $this->getCurrentUser()->getId();

        $feedback= $this->get('mfb_feedback.service')->createNewFeedback($accountId);
        $form = $this->getCustomerForm($customer);

        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'customerEmail' => $customer->getEmail(),
                'form' => $form->createView()
            )
        );
    }

    public function saveFeedbackActionCP(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        $customerEmail = null;
        try {
            $customer = $this->get('mfb_customer.service')->createNewCustomer($accountId);
            $form = $this->getCustomerForm($customer);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }

            $this->get('mfb_customer.service')->store($customer);
            $customerEmail = $customer->getEmail();
        } catch (AccountException $ax) {
            $form->get('email')->addError(new FormError('Email already exists'));
        } catch (\Exception $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'customerEmail' => $customerEmail,
                'form' => $form->createView()
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
     * @param $accountId
     * @param $accountChannelId
     * @return \Symfony\Component\Form\Form
     */
    private function getFeedbackForm(Feedback $feedback, $accountId, $accountChannelId)
    {
        $serviceGroup = $this->get('mfb_service.service')->getServiceGroupEntity($accountChannelId);
        $serviceProvider = $this->get('mfb_service.service')->getServiceProviderEntity($accountChannelId);

        $form = $this->createForm(new FeedbackType(new ServiceType($serviceProvider, $serviceGroup)), $feedback, array(
            'action' => $this->generateUrl('mfb_feedback_save', array(
                        'accountId' => $accountId,
                        'accountChannelId' => $accountChannelId
                    )),
                'method' => 'POST'
            ));
        return $form;
    }
}
