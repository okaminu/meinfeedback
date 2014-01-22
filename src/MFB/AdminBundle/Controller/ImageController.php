<?php

namespace MFB\AdminBundle\Controller;

use MFB\DocumentBundle\DocumentException;
use MFB\DocumentBundle\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends Controller
{
    public function showAction()
    {
        $document = $this->createNewLogo($this->getAccountChannel()->getId());
        $form = $this->createLogoForm($document);
        return $this->showImageForm($form);
    }

    public function saveLogoAction(Request $request)
    {
        $document = $this->createNewLogo($this->getAccountChannel()->getId());
        $form = $this->createLogoForm($document);

        $form->handleRequest($request);
        try {
            if (!$form->isValid($request)) {
                throw new Exception('');
            }
            $this->get('mfb_document.service')->storeSingleForCategory($document);
            return $this->redirect($this->generateUrl('mfb_admin_images_show'));
        } catch (DocumentException $ex) {
            $form->addError(new FormError($ex->getMessage()));
        } catch (Exception $ex) {
            $form->addError(new FormError('Cannot upload'));
        }
        return $this->showImageForm($form);
    }

    public function removeLogoAction()
    {
    }

    private function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    private function getAccountChannel()
    {
        $accountId = $this->getCurrentUser()->getId();
        return $this->get('mfb_account_channel.service')->findByAccountId($accountId);
    }

    private function createLogoForm($document)
    {
        $form = $this->createForm(
            new DocumentType(),
            $document,
            array(
                'action' => $this->generateUrl('mfb_admin_image_save_logo'),
                'method' => 'POST'
            )
        );
        return $form;
    }

    private function createNewLogo($channelId)
    {
        return $this->get('mfb_document.service')->createNewImage($channelId, 'logo');
    }

    private function showImageForm($form)
    {
        $channelId = $this->getAccountChannel()->getId();
        $documents = $this->get('mfb_document.service')->findByCategory($channelId, 'logo');

        $logoPath = '';
        if ($document = end($documents)) {
            $logoPath = $document->getWebPath();
        }

        return $this->render(
            'MFBAdminBundle:Image:show.html.twig',
            array(
                'form' => $form->createView(),
                'logoUrl' => $logoPath
            )
        );
    }

}
