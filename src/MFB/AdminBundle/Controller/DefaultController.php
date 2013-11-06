<?php

namespace MFB\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\ChannelBundle\Form\AccountChannelType;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use MFB\EmailBundle\Entity\EmailTemplate;
use MFB\FeedbackBundle\Entity\FeedbackInvite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
            $feedbackList = $em->getRepository('MFBFeedbackBundle:Feedback')->findBy(
                array(
                    'accountId' => $accountId
                ),
                array('id'=>'DESC')
            );

        return $this->render(
            'MFBAdminBundle:Default:index.html.twig',
            array(
                'feedbackList'=>$feedbackList
            )
        );
    }
    public function locationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $entity = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(array('accountId'=>$accountId));
        if (!$entity) {
            $entity = new AccountChannel();
            $entity->setAccountId($accountId);
        }

        $form = $this->createForm(new AccountChannelType(), $entity, array(
                'action' => $this->generateUrl('mfb_location'),
                'method' => 'POST',
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();


            return $this->redirect($this->generateUrl('mfb_location'));
        }

        return $this->render('MFBAdminBundle:Default:location.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
    }

    public function customerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $this->get('security.context')->getToken();
        $accountId = $token->getUser()->getId();

        $entity = new Customer();
        $entity->setAccountId($accountId);

        $form = $this->createForm(new CustomerType(), $entity, array(
                'action' => $this->generateUrl('mfb_add_customer'),
                'method' => 'POST',
            ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();

                $accountChannel = $em->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
                    array('accountId'=>$accountId)
                );

                $invite = new FeedbackInvite();
                $invite->setAccountId($entity->getAccountId());
                $invite->setCustomerId($entity->getId());
                $invite->setChannelId($accountChannel->getId());
                $invite->updatedTimestamps();
                $em->persist($invite);
                $em->flush();

                $emailTemplate = $em->getRepository('MFBEmailBundle:EmailTemplate')->findOneBy(
                    array(
                        'accountId' => $accountId,
                        'name' => 'AccountChannel'
                    )
                );
                if (!$emailTemplate) {
                    $emailTemplate = new EmailTemplate();
                    $emailTemplate->setTitle($this->get('translator')->trans('Please leave feedback'));
                    $emailTemplate->setTemplateCode(
                        $this->get('translator')->trans('default_account_channel_template')
                    );
                }

                $inviteUrl  = $this->generateUrl(
                    'mfb_feedback_create_with_invite',
                    array('token' => $invite->getToken()),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $this->get('mfb_email.sender')->createForAccountChannel(
                    $entity,
                    $accountChannel,
                    $emailTemplate,
                    $inviteUrl,
                    $entity
                );

                return $this->redirect(
                    $this->generateUrl('mfb_add_customer', array('added_email' => $entity->getEmail()))
                );
            } catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000 ) {
                    $form->get('email')->addError( new FormError('Email already exists'));
                } else {
                    $form->addError( new FormError($ex->getMessage()));
                }

            }
        }

        return $this->render('MFBAdminBundle:Default:customer.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'added_email' => $request->get('added_email')
            ));
    }
}
