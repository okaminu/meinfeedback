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
//            throw new Exception('Upload error');
              throw $ex;
        }
    }

    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}