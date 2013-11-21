<?php

namespace MFB\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use MFB\FeedbackBundle\Form\FeedbackType;
use MFB\FeedbackBundle\Manager\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MFB\ServiceBundle\Manager\Service as ServiceEntityManager;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use MFB\FeedbackBundle\Manager\FeedbackInvite as FeedbackInviteManager;
use Doctrine\ORM\NoResultException;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $accountId = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

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
        $requestForm = $request->get('mfb_customerbundle_customer');
        $serviceIdReference = $requestForm['serviceIdReference'];
        $serviceDescription = $requestForm['serviceDescription'];
        $serviceDate = $requestForm['serviceDate'];

        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $customer = new Customer();
        $customer->setAccountId($accountId);

        $form = $this->getCustomerForm($customer);

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

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($customer);
                $em->flush();

                $invite = $this->saveFeedbackInvite($customer, $accountChannel, $em);

                $serviceEntity = $this->saveService(
                    $serviceDate,
                    $accountId,
                    $accountChannel->getId(),
                    $customer,
                    $serviceDescription,
                    $serviceIdReference,
                    $em
                );

                $emailTemplate = $this->getEmailTemplate($em, $accountId);

                $inviteUrl = $this->generateUrl(
                    'mfb_feedback_create_with_invite',
                    array('token' => $invite->getToken()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $this->get('mfb_email.sender')->createForAccountChannel(
                    $customer,
                    $accountChannel,
                    $emailTemplate,
                    $inviteUrl,
                    $serviceEntity
                );

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
                'feedback' => $request->get('feedback')
            )
        );
    }

    /**
     * @param $em
     * @param $accountId
     * @return EmailTemplate
     */
    private function getEmailTemplate($em, $accountId)
    {
        $emailTemplate = $em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
            array(
                'accountId' => $accountId,
                'name' => 'AccountChannel',
            )
        );

        if (!$emailTemplate) {
            $emailTemplate = new EmailTemplate();
            $emailTemplate->setTitle($this->get('translator')->trans('Please leave feedback'));
            $emailTemplate->setTemplateCode(
                $this->get('translator')->trans('default_account_channel_template')
            );
        }
        return $emailTemplate;
    }

    /**
     * @param $customer
     * @param $accountChannel
     * @param $em
     * @return FeedbackInvite
     */
    private function saveFeedbackInvite($customer, $accountChannel, $em)
    {
        $emailEntity = new FeedbackInviteManager(
            $customer->getAccountId(),
            $accountChannel->getId(),
            $customer->getId(),
            new FeedbackInvite()
        );

        $invite = $emailEntity->createEntity($customer, $accountChannel);
        $em->persist($invite);
        $em->flush();
        return $invite;
    }

    /**
     * @param $serviceDate
     * @param $accountId
     * @param $accountChannelId
     * @param $customer
     * @param $serviceDescription
     * @param $serviceIdReference
     * @param $em
     * @return ServiceEntity
     */
    private function saveService(
        $serviceDate,
        $accountId,
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
            $accountId,
            $accountChannelId,
            $customer,
            $serviceDescription,
            $serviceDateTime,
            $serviceIdReference,
            new ServiceEntity()
        );

        $serviceEntity = $serviceEntityManager->createEntity();

        $em->persist($serviceEntity);
        $em->flush();
        return $serviceEntity;
    }

    /**
     * @param $customer
     * @return \Symfony\Component\Form\Form
     */
    private function getCustomerForm($customer)
    {
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



}
