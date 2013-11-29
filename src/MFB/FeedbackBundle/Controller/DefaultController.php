<?php

namespace MFB\FeedbackBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\FeedbackBundle\Event\CustomerAccountEvent;
use MFB\FeedbackBundle\FeedbackEvents;
use MFB\FeedbackBundle\FeedbackException;
use MFB\FeedbackBundle\Manager\Feedback as FeedbackEntityManager;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\ServiceBundle\Manager\Service as ServiceEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $customer = new Customer();
        $customer->setAccountId($account->getId());
        $form = $this->createForm(new CustomerType(), $customer);

        return $this->showFeedbackForm($account->getId(), $accountChannel, $form->createView());
    }

    public function saveAction(Request $request)
    {
        $rating = null;
        $errorMessage = null;
        $requestForm = $request->get('mfb_customerbundle_customer');
        $serviceIdReference = $requestForm['serviceIdReference'];
        $serviceDescription = $requestForm['serviceDescription'];
        $serviceDate = $requestForm['serviceDate'];
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->container->get('event_dispatcher');


        $dispatcher->dispatch(FeedbackEvents::REGULAR_INITIALIZE);

        /** @var Account $account */
        $account = $this->get("mfb_account.manager")->findAccountByAccountId($request->get('accountId'));
        /** @var AccountChannel $accountChannel */
        $accountChannel = $this->get("mfb_account_channel.manager")->findAccountChannelByAccount($account);

        $customer = new Customer();
        $customer->setAccountId($account->getId());

        $form = $this->createForm(new CustomerType(), $customer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {

                $em->persist($customer);

                $feedbackId = $this->saveFeedback($request, $account, $accountChannel, $customer, $em);

                $this->saveService(
                    $serviceDate,
                    $account,
                    $accountChannel->getId(),
                    $customer,
                    $serviceDescription,
                    $serviceIdReference,
                    $em
                );

                $em->flush();

                $event = new CustomerAccountEvent($feedbackId, $account, $customer, $request);
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
                    $form->get('email')->addError(new FormError('Email already exists'));
                } else {
                    $form->addError(new FormError($ex->getMessage()));
                }
            } catch (FeedbackException $ex){
                $errorMessage = $ex->getMessage();
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
     * @param $serviceDate
     * @param $account
     * @param $accountChannelId
     * @param $customer
     * @param $serviceDescription
     * @param $serviceIdReference
     * @param $em
     */
    protected function saveService(
        $serviceDate,
        $account,
        $accountChannelId,
        $customer,
        $serviceDescription,
        $serviceIdReference,
        $em
    ) {
        $serviceDateTime = null;
        if ($serviceDate['year'] != "" &&
            $serviceDate['month'] != "" &&
            $serviceDate['day'] != ""
        ) {
            $serviceDateTime = new \DateTime(implode('-', $serviceDate));
        }

        $serviceEntityManager = new ServiceEntityManager(
            $account->getId(),
            $accountChannelId,
            $customer,
            $serviceDescription,
            $serviceDateTime,
            $serviceIdReference,
            new ServiceEntity()
        );

        $serviceEntity = $serviceEntityManager->createEntity();
        $em->persist($serviceEntity);
    }

    /**
     * @param Request $request
     * @param $account
     * @param $accountChannel
     * @param $customer
     * @param $em
     * @return int
     */
    protected function saveFeedback(Request $request, $account, $accountChannel, $customer, $em)
    {
        $feedbackEntityManager = new FeedbackEntityManager(
            $account->getId(),
            $accountChannel->getId(),
            $customer,
            $request->get('feedback'),
            $request->get('rating'),
            new FeedbackEntity()
        );

        return $feedbackEntityManager->saveFeedback(
            $em,
            $accountChannel->getRatingsEnabled()
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
}
