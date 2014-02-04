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

    public function store($channel)
    {
        try {
            if ($this->areNoVisibleServiceTypes($channel)) {
                throw new ChannelException('At least one service must be visible');
            }

            $this->saveEntity($channel);
        } catch (DBALException $ex) {
            throw new ChannelException('Cannot save channel information');
        }
    }

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

    private function areNoVisibleServiceTypes($channel)
    {
        $serviceTypes = $channel->getServiceType();
        if ($serviceTypes != null) {
            $count = $this->getInvisibleServiceTypes($serviceTypes);
            if (count($serviceTypes) == $count) {
                return true;
            }
        }
        return false;
    }

    private function getInvisibleServiceTypes($serviceTypes)
    {
        $count = 0;
        foreach ($serviceTypes as $type) {
            if ($type->getVisibility() == false) {
                $count++;
            }
        }
        return $count;
    }
}
