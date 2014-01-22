<?php
namespace MFB\DocumentBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\Service\Channel as ChannelService;
use Symfony\Component\Config\Definition\Exception\Exception;
use MFB\DocumentBundle\Entity\Document as DocumentEntity;

class Document
{
    private $entityManager;
    private $channelService;

    public function __construct(EntityManager $em, ChannelService $cs)
    {
        $this->entityManager = $em;
        $this->channelService = $cs;
    }

    public function createNewDocument($channelId, $category, $type)
    {
        $accountChannel = $this->channelService->findById($channelId);

        $document = new DocumentEntity();
        $document->setChannel($accountChannel);
        $document->setCategory($category);
        $document->setFiletype($type);
        return $document;
    }


    public function store($document)
    {
        try {
            $this->saveEntity($document);
        } catch (DBALException $ex) {
            throw new Exception('Upload error');
        }
    }

    public function storeSingleForCategory($document)
    {
        try {
            $this->removeCategoryDocuments($document->getChannel()->getId(), $document->getCategory());
            $this->saveEntity($document);
        } catch (DBALException $ex) {
            throw new Exception('Upload error');
        }
    }

    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function removeCategoryDocuments($channelId, $category)
    {
        $documents = $this->findByCategory($channelId, $category);

        foreach ($documents as $document) {
            $this->entityManager->remove($document);
        }
        $this->entityManager->flush();
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
}
