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
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\CustomerBundle\Entity\Customer", cascade={"persist"})
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     **/
    private $customer;


    /**
     * @var string
     *
     * @ORM\Column(name="service_id_reference", type="string", nullable=true)
     */
    private $serviceIdReference;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ChannelBundle\Entity\ChannelServiceType")
     * @ORM\JoinColumn(name="channel_service_type_id", referencedColumnName="id")
     **/
    private $channelServiceType;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceProvider")
     * @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     **/
    private $serviceProvider = null;


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

    /**
     * Set customer
     *
     * @param \MFB\CustomerBundle\Entity\Customer $customer
     * @return Service
     */
    public function setCustomer(\MFB\CustomerBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \MFB\CustomerBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }


    /**
     * Set serviceProvider
     *
     * @param \MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider
     * @return Service
     */
    public function setServiceProvider(\MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider = null)
    {
        $this->serviceProvider = $serviceProvider;
    
        return $this;
    }

    /**
     * Get serviceProvider
     *
     * @return \MFB\ServiceBundle\Entity\ServiceProvider 
     */
    public function getServiceProvider()
    {
        return $this->serviceProvider;
    }

    /**
     * Set channelServiceType
     *
     * @param \MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType
     * @return Service
     */
    public function setChannelServiceType(\MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType = null)
    {
        $this->channelServiceType = $channelServiceType;
    
        return $this;
    }

    /**
     * Get channelServiceType
     *
     * @return \MFB\ChannelBundle\Entity\ChannelServiceType 
     */
    public function getChannelServiceType()
    {
        return $this->channelServiceType;
    }

    public function getServiceType()
    {
        return $this->channelServiceType->getServiceType();
    }

}