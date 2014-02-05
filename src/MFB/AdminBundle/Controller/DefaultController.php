<?php

namespace MFB\AdminBundle\Controller;


use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\ServiceBundle\Entity\Service;
use MFB\ServiceBundle\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/show_create_customer", name="mfb_show_create_customer")
     * @Route("/", name="mfb_admin_homepage")
     * @Route("/login_check", name="mfb_admin_login_check")
     * @Route("/logout", name="mfb_admin_logout")
     * @Template
     */
    public function indexAction()
    {
        $accountId = $this->getCurrentUser()->getId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        $channelFeedbacks = $this->get('mfb_feedback_display.service')->getChannelFeedbacks($channel->getId());
        $channelFeedbacks->setElementsPerPage($this->container->getParameter('mfb_feedback.maxFeedbacks'));

        return array(
                'feedbackSummaryPage' => $channelFeedbacks->getFeedbackSummary(),
                'ratingCount' => $channelFeedbacks->getChannelFeedbackCount(),
                'channelRatingSummaryList' => $channelFeedbacks->getChannelRatingSummary()
        );
    }

    /**
     * @Route("/save_feedback_activation", name="mfb_save_feedback_activation")
     * @Method({"POST"})
     */
    public function saveFeedbackActivationAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        $feedbackService = $this->get('mfb_feedback.service');
        $channelFeedbacks = $this->get('mfb_feedback_display.service')->getChannelFeedbacks($channel->getId());
        $channelFeedbacks->setElementsPerPage($this->container->getParameter('mfb_feedback.maxFeedbacks'));
        $activates = $request->request->get('activate');

        $feedbackService->batchActivate($activates, $channelFeedbacks->getFeedbackSummary()->getItems());
        return $this->redirect($this->generateUrl('mfb_admin_homepage'));
    }


    /**
     * @Route("/about_me", name="mfb_location")
     * @Template
     */
    public function locationAction(Request $request)
    {
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($this->getCurrentUser()->getId());
        $form = $this->createForm(new AccountChannelType(), $channel);
        $form->handleRequest($request);

        try {
            if ($form->isValid()) {
                $this->get('mfb_account_channel.service')->store($channel);
            }
        } catch (ChannelException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return array(
                'entity' => $channel,
                'form' => $form->createView(),
        );
    }

    public function showCreateServiceCustomerFormAction()
    {
        $channel = $this->getChannel();

        $service = $this->get('mfb_service.service')->createNewService($channel->getId());
        $form = $this->getServiceForm($service, $channel->getId());

        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'customerEmail' => $service->getCustomer()->getEmail(),
                'form' => $form->createView()
            )
        );
    }

    /**
     * @Route("/save_customer", name="mfb_save_customer")
     * @Template
     */
    public function customerAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        $customerEmail = null;
        try {
            $channelId = $this->getChannel()->getId();
            $service = $this->get('mfb_service.service')->createNewService($channelId);
            $form = $this->getServiceForm($service, $channelId);
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new \Exception('Not valid form');
            }
            $this->get('mfb_service.service')->store($service);
            $customer = $service->getCustomer();
            $this->get('mfb_feedback_invite.service')->createSaveFeedbackInvite(
                $accountId,
                $customer->getId(),
                $service
            );

            $customerEmail = $customer->getEmail();
        } catch (ServiceException $ax) {
            $form->get('customer')->get('email')->addError(new FormError('Email already exists'));
        } catch (\Exception $ex) {
            $form->addError(new FormError($ex->getMessage()));
        }

        return array(
                'customerEmail' => $customerEmail,
                'form' => $form->createView()
        );
    }

    /**
     * @Route("/change_password", name="mfb_admin_change_password")
     * @Template
     */

    public function changePasswordAction(Request $request)
    {
        $account = $this->getCurrentUser();

        $form = $this->get("mfb_account.change_password.form.factory")->createForm();

        $form->setData($account);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->get('mfb_account.encoder')->encodePassword($account);
                $this->get('mfb_account.service')->addAccount($account);

                $passwordChanged = $this->get('translator')->trans("Password changed");

                /** var SessionInterface $session  */
                $this->getRequest()->getSession()->getFlashBag()->add('success', $passwordChanged);
                return $this->redirect($this->generateUrl('mfb_admin_success'));
            }
        }

        return array('form' => $form->createView());
    }


    /**
     * @Route("/sucess", name="mfb_admin_success")
     * @Template
     */

    public function successAction()
    {
        return array('message' => 'Password successfully changed');
    }

    private function getServiceForm(Service $service, $channelId)
    {
        $serviceType = $this->get('mfb_service.service')->getServiceFormType($channelId);
        $form = $this->createForm(
            $serviceType,
            $service,
            array(
                'action' => $this->generateUrl('mfb_save_customer'),
                'method' => 'POST',
            )
        );

        $form->get('customer')->add('salutation', 'text', array('required' => false));
        return $form;
    }

    private function saveEntity($em, $entity)
    {
        $em->persist($entity);
        $em->flush();
    }

    private function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    private function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    private function getAccountChannel($em, $accountId)
    {
        $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        return $accountChannel;
    }

    /**
     * @return AccountChannel
     */
    private function getChannel()
    {
        $accountId = $this->getCurrentUser()->getId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);
        return $channel;
    }


}
