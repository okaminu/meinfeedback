<?php

namespace MFB\AdminBundle\Controller;

use MFB\DocumentBundle\DocumentException;
use MFB\DocumentBundle\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ImageController extends Controller
{

    /**
     * @Route("/admin_images_show", name="mfb_admin_images_show")
     * @Template
     */

    public function showAction()
    {
        $document = $this->createNewLogo($this->getAccountChannel()->getId());
        $form = $this->createLogoForm($document);
        return $this->showImageForm($form);
    }

    /**
     * @Route("/admin_image_save_logo", name="mfb_admin_image_save_logo")
     * @Template("MFBAdminBundle:Image:show.html.twig")
     */
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

        return array(
                'form' => $form->createView(),
                'logoUrl' => $logoPath,
                'allowedImageExtensions' => $this->get('mfb_document.service')->getTypeExtensionWhitelist('image')
        );
    }

}
