<?php

namespace MFB\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\CustomerBundle\CustomerEvents;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Event\NewCustomerEvent;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\FeedbackBundle\Specification\PreBuiltSpecification;
use MFB\ServiceBundle\Entity\Service;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $account = $this->get('security.context')->getToken()->getUser();
        $accountId = $account->getId();
        $em = $this->getDoctrine()->getManager();

        /** @var AccountChannel $accountChannel */
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId'=>$accountId)
        );

        $preBuiltSpec = new PreBuiltSpecification($account, $accountChannel);
        $feedbackRatingSpecification = $preBuiltSpec->getFeedbackWithRatingSpecification();

        $feedbackRepo = $em->getRepository('MFBFeedbackBundle:Feedback');
        $feedbackCount = $feedbackRepo->getFeedbackCount($feedbackRatingSpecification);
        $feedbackRatingAverage = round($feedbackRepo->getRatingsAverage($feedbackRatingSpecification), 1);

        $feedbackList = $em
            ->getRepository('MFBFeedbackBundle:Feedback')
            ->findSortedByAccountId($accountId);

        if ($request->getMethod() == 'POST') {
            $activates = $request->request->get('activate');
            $em->getRepository('MFBFeedbackBundle:Feedback')->batchActivate($activates, $feedbackList, $em);
            return $this->redirect($this->generateUrl('mfb_admin_homepage'));
        }

        return $this->render(
            'MFBAdminBundle:Default:index.html.twig',
            array(
                'feedbackList' => $feedbackList,
                'ratingCount' => $feedbackCount,
                'ratingAverage' => $feedbackRatingAverage
            )
        );
    }

    public function locationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $entity = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(array('accountId' => $accountId));
        if (!$entity) {
            $entity = new AccountChannel();
            $entity->setAccountId($accountId);
        }
        $form = $this->createForm(
            new AccountChannelType(),
            $entity,
            array(
                'action' => $this->generateUrl('mfb_location'),
                'method' => 'POST',
            )
        );
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('mfb_location'));
        }

        return $this->render(
            'MFBAdminBundle:Default:location.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    public function customerAction(Request $request)
    {
        $dispatcher = $this->container->get('event_dispatcher');

        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        if ($accountChannel === null) {
            return $this->render(
                'MFBAdminBundle:Default:error.html.twig',
                array(
                    'errorMessage' =>
                        $this->get('translator')->trans('No account data found. Please fill Account setup form.')
                )
            );
        }
        $customer = new Customer();
        $customer->setAccountId($accountId);

        $service = new Service();
        $service->setAccountId($accountId);
        $service->setChannelId($accountChannel->getId());

        $form = $this->getCustomerForm($customer, $service);
        $dispatcher->dispatch(CustomerEvents::CREATE_CUSTOMER_INITIALIZE);

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($customer);
                $em->flush();

                $invite = $this->saveFeedbackInvite($accountId, $customer, $accountChannel, $em);

                $inviteUrl = $this->generateUrl(
                    'mfb_feedback_create_with_invite',
                    array('token' => $invite->getToken()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $event = new NewCustomerEvent($customer, $accountChannel, $service, $inviteUrl);
                $dispatcher->dispatch(CustomerEvents::CREATE_CUSTOMER_COMPLETE, $event);

                return $this->redirect(
                    $this->generateUrl('mfb_add_customer', array('added_email' => $customer->getEmail()))
                );
            } catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                    $form->get('email')->addError(new FormError('Email already exists'));
                } else {
                    $form->addError(new FormError($ex->getMessage()));
                }

            }
        }
        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'entity' => $customer,
                'form' => $form->createView(),
                'added_email' => $request->get('added_email'),
            )
        );
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $account = $this->get('security.context')->getToken()->getUser();

        $form = $this->get("mfb_account.change_password.form.factory")->createForm();

        $form->setData($account);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('mfb_account.encoder')->encodePassword($account);
                $this->get('mfb_account.service')->addAccount($account);

                /** var SessionInterface $session  */
                $this->getRequest()->getSession()->getFlashBag()->add('success', 'Password successfully changed');
                return $this->redirect($this->generateUrl('mfb_admin_success'));
            }
        }

        return $this->render(
            'MFBAccountBundle:ChangePassword:changePassword.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }


    public function successAction()
    {
        return $this->render(
            'MFBAccountBundle:Default:success.html.twig',
            array('message' => 'Password successfully changed')
        );
    }

    /**
     * @param $customer
     * @param $service
     * @return \Symfony\Component\Form\Form
     */
    private function getCustomerForm(Customer $customer, Service $service)
    {
        $customer->addService($service);
        $service->setCustomer($customer);
        $form = $this->createForm(
            new CustomerType(),
            $customer,
            array(
                'action' => $this->generateUrl('mfb_add_customer'),
                'method' => 'POST',
            )
        );

        $form->add('salutation', 'text', array('required' => false));
        return $form;
    }

    /**
     * @param $accountId
     * @param $customer
     * @param $accountChannel
     * @param $em
     * @return FeedbackInvite
     */
    private function saveFeedbackInvite($accountId, $customer, $accountChannel, $em)
    {
        $invite = new FeedbackInvite();
        $invite->setAccountId($accountId);
        $invite->setCustomerId($customer->getId());
        $invite->setChannelId($accountChannel->getId());
        $invite->updatedTimestamps();
        $em->persist($invite);
        $em->flush();
        return $invite;
    }


}
