<?php

namespace MFB\AdminBundle\Controller;

use MFB\DocumentBundle\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends Controller
{
    public function showAction()
    {
        $document = $this->createNewLogoDocument($this->getAccountChannel()->getId());
        $form = $this->createLogoForm($document);

        return $this->render(
            'MFBAdminBundle:Image:show.html.twig',
            array('form' => $form->createView())
        );
    }

    public function saveLogoAction(Request $request)
    {
        $document = $this->createNewLogoDocument($this->getAccountChannel()->getId());
        $form = $this->createLogoForm($document);

        $form->handleRequest($request);

        if ($form->isValid($request)) {
            $this->get('mfb_document.service')->store($document);
        }

        $this->redirect($this->generateUrl('mfb_admin_images_show'));
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

    private function createNewLogoDocument($channelId)
    {
        $document = $this->get('mfb_document.service')->createNewDocument(
            $channelId,
            'logo',
            'image'
        );
        return $document;
    }

}
