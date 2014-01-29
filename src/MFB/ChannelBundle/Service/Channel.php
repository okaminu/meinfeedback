<?php
namespace MFB\ChannelBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use MFB\ChannelBundle\ChannelException;
use MFB\ChannelBundle\Entity\AccountChannel;
use MFB\CountryBundle\Service\Country;

class Channel
{
    private $entityManager;
    private $countryService;

    public function __construct(EntityManager $em, Country $countryService)
    {
        $this->entityManager = $em;
        $this->countryService = $countryService;
    }

    public function createNew($accountId)
    {
        $accountChannel = new AccountChannel();
        $accountChannel->setAccountId($accountId);

        $countries = $this->countryService->findAll();
        $accountChannel->setCountry($countries[0]);
        return $accountChannel;
    }

    public function createStoreNew($accountId)
    {
        $accountChannel = $this->createNew($accountId);
        $this->store($accountChannel);
    }

    public function store($channel)
    {
        try {
            $this->saveEntity($channel);
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot store channel data');
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

    public function findByAccountId($accountId)
    {
        $accountChannel = $this->entityManager->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('accountId' => $accountId)
        );
        return $accountChannel;
    }

    public function findById($channelId)
    {
        $accountChannel = $this->entityManager->getRepository('MFBChannelBundle:AccountChannel')->findOneBy(
            array('id' => $channelId)
        );
        return $accountChannel;
    }

}
