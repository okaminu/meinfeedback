<?php

namespace MFB\FeedbackBundle\Controller;

use MFB\FeedbackBundle\Entity\Feedback as FeedbackEntity;
use MFB\ServiceBundle\Entity\Service as ServiceEntity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MFB\AccountBundle\Entity\Account;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CustomerBundle\Entity\Customer;
use MFB\CustomerBundle\Form\CustomerType;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\DBALException;
use MFB\ServiceBundle\Manager\Service as ServiceEntityManager;
use MFB\FeedbackBundle\Manager\Feedback as FeedbackEntityManager;
use MFB\Template\ThankYouTemplate;
use MFB\Template\Manager\TemplateManager;

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
            array('accountId'=>$account->getId())
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
        $requestForm = $request->get('mfb_customerbundle_customer');
        $serviceIdReference = $requestForm['serviceIdReference'];
        $serviceDescription = $requestForm['serviceDescription'];
        $serviceDate = $requestForm['serviceDate'];
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

                $feedbackEntityManager = new FeedbackEntityManager(
                    $account->getId(),
                    $accountChannel->getId(),
                    $customer,
                    $request->get('feedback'),
                    $request->get('rating'),
                    new FeedbackEntity()
                );

                $feedbackEntity = $feedbackEntityManager->createEntity();

                if (($accountChannel->getRatingsEnabled() == '1') && (is_null($feedbackEntity->getRating()))) {
                    return $this->showFeedbackForm(
                        $account->getId(),
                        $accountChannel,
                        $form->createView(),
                        $request->get('feedback'),
                        'Please select star rating'
                    );
                }

                $serviceDateTime = null;
                if ($serviceDate['year'] != "" &&
                    $serviceDate['month'] != "" &&
                    $serviceDate['day'] != "") {
                    $serviceDateTime = new \DateTime(implode('-', $serviceDate));
                }

                $serviceEntityManager = new ServiceEntityManager(
                    $account->getId(),
                    $accountChannel->getId(),
                    $customer,
                    $serviceDescription,
                    $serviceDateTime,
                    $serviceIdReference,
                    new ServiceEntity()
                );

                $serviceEntity = $serviceEntityManager->createEntity();
                $em->persist($customer);
                $em->persist($feedbackEntity);
                $em->persist($serviceEntity);

                //$em->flush();

                $templateManager = new TemplateManager();
                $templateEntity = $templateManager->getTemplate(
                    $account->getId(),
                    $templateManager::THANKYOU_TEMPLATE_TYPE,
                    'ThankYouPage',
                    $em,
                    $this->get('translator')
                );

                $template = new ThankYouTemplate();
                $templateText = $template
                    ->setContent($templateEntity->getTemplateCode())
                    ->setCustomer($customer)
                    ->getTranslation();

                return $this->render(
                    'MFBFeedbackBundle:Invite:thank_you.html.twig',
                    array(
                        'thankyou_text' => $templateText,
                    )
                );

            } catch (DBALException $ex) {
                $ex = $ex->getPrevious();
                if ($ex instanceof \PDOException && $ex->getCode() == 23000) {
                    $form->get('email')->addError(new FormError('Email already exists'));
                } else {
                    $form->addError(new FormError($ex->getMessage()));
                }
            }
            return $this->showFeedbackForm($account->getId(), $accountChannel, $form->createView());
        }
        return $this->render('MFBFeedbackBundle:Invite:invalid_data.html.twig');
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
