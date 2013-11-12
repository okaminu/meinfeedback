<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Service
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var integer
     *
     * @ORM\Column(name="channel_id", type="integer")
     */
    private $channelId;

    
    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="service_id_reference", type="string", nullable=true)
     */
    private $serviceIdReference;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     * @return Service
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    
        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer 
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set channelId
     *
     * @param integer $channelId
     * @return Service
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    
        return $this;
    }

    /**
     * Get channelId
     *
     * @return integer 
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Service
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Service
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set serviceIdReference
     *
     * @param string $serviceIdReference
     * @return Service
     */
    public function setServiceIdReference($serviceIdReference)
    {
        $this->serviceIdReference = $serviceIdReference;
    
        return $this;
    }

    /**
     * Get serviceIdReference
     *
     * @return string 
     */
    public function getServiceIdReference()
    {
        return $this->serviceIdReference;
    }
}