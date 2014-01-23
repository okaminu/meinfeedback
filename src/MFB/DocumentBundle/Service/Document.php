<?php
namespace MFB\DocumentBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel as ChannelService;
use Symfony\Component\Config\Definition\Exception\Exception;
use MFB\DocumentBundle\Entity\Document as DocumentEntity;

class Document
{
    private $entityManager;
    private $channelService;
    private $extensionWhitelist;

    public function __construct(EntityManager $em, ChannelService $cs, $ew)
    {
        $this->entityManager = $em;
        $this->channelService = $cs;
        $this->extensionWhitelist = $ew;
    }

    private function createNewDocument($channelId, $category, $type)
    {
        $accountChannel = $this->channelService->findById($channelId);

        $document = new DocumentEntity();
        $document->setChannel($accountChannel);
        $document->setCategory($category);
        $document->setFiletype($type);
        $document->setExtensionWhitelist($this->extensionWhitelist);
        return $document;
    }

    public function createNewImage($channelId, $category)
    {
        return $this->createNewDocument($channelId, $category, 'image');
    }


    public function store($document)
    {
        try {
            $this->entityManager->persist($document);

            $this->entityManager->flush();
        } catch (DBALException $ex) {
            throw new Exception('Upload error');
        }
    }

    public function storeSingleForCategory($document)
    {
        try {
            $this->removeCategoryDocuments($document->getChannel()->getId(), $document->getCategory());
            $this->entityManager->persist($document);

            $this->entityManager->flush();
        } catch (DBALException $ex) {
            throw new Exception('Upload error');
        }
    }

    private function removeCategoryDocuments($channelId, $category)
    {
        $documents = $this->findByCategory($channelId, $category);

        foreach ($documents as $document) {
            $this->entityManager->remove($document);
        }
    }

    public function findByCategory($channelId, $category)
    {
        $documents = $this->entityManager->getRepository("MFBDocumentBundle:Document")->findBy(
            array(
                'channel' => $channelId,
                'category' => $category
            )
        );
        return $documents;
    }
    
    public function getTypeExtensionWhitelist($type)
    {
            return $this->extensionWhitelist[$type];
    }
}
