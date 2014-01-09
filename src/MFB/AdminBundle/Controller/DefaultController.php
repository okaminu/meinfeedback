<?php

namespace MFB\AdminBundle\Controller;


use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\ServiceBundle\Entity\Service;
use MFB\ServiceBundle\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $accountId = $this->getCurrentUser()->getId();
        $channel = $this->get('mfb_account_channel.service')->findByAccountId($accountId);

        $feedbackService = $this->get('mfb_feedback_display.service');

        return $this->render(
            'MFBAdminBundle:Default:index.html.twig',
            array(
                'feedbackSummaryList' => $feedbackService->getFeedbackSummaryList($channel->getId()),
                'ratingCount' => $feedbackService->getChannelFeedbackCount($channel->getId()),
                'channelRatingSummaryList' => $feedbackService->createChannelRatingSummary($channel->getId())
            )
        );
    }

    public function saveFeedbackActivationAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        $feedbackService = $this->get('mfb_feedback.service');
        $feedbackDisplayService = $this->get('mfb_feedback_display.service');
        if ($request->getMethod() == 'POST') {
            $activates = $request->request->get('activate');
            $feedbackService->batchActivate($activates, $feedbackDisplayService->getFeedbackList($accountId));
            return $this->redirect($this->generateUrl('mfb_admin_homepage'));
        }
    }


    public function locationAction(Request $request)
    {
        $em = $this->getEntityManager();

        $accountId = $this->getCurrentUser()->getId();

        $entity = $this->getAccountChannel($em, $accountId);
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
            $this->saveEntity($em, $entity);
            return $this->redirect($form->get('redirect')->getData());
        }

        return $this->render(
            'MFBAdminBundle:Default:location.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    public function showCreateServiceCustomerFormAction()
    {
        $accountId = $this->getCurrentUser()->getId();

        $service = $this->get('mfb_service.service')->createNewService($accountId);
        $form = $this->getServiceForm($service, $accountId);

        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'customerEmail' => $service->getCustomer()->getEmail(),
                'form' => $form->createView()
            )
        );
    }

    public function saveServiceCustomerAction(Request $request)
    {
        $accountId = $this->getCurrentUser()->getId();
        $customerEmail = null;
        try {
            $service = $this->get('mfb_service.service')->createNewService($accountId);
            $form = $this->getServiceForm($service, $accountId);
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

        return $this->render(
            'MFBAdminBundle:Default:customer.html.twig',
            array(
                'customerEmail' => $customerEmail,
                'form' => $form->createView()
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
     * @param $service
     * @param $accountId
     * @return \Symfony\Component\Form\Form
     */
    private function getServiceForm(Service $service, $accountId)
    {
        $serviceType = $this->get('mfb_service.service')->getServiceType($accountId);
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

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
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




}
