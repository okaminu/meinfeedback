<?php

namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MFB\ChannelBundle\Entity\AccountChannel;

/**
 * ChannelServiceType
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ChannelServiceType
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ChannelBundle\Entity\AccountChannel",
     * inversedBy="channelServiceType", cascade={"persist"})
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     **/
    private $channel;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceType",
     * inversedBy="channelServiceType", cascade={"persist"})
     * @ORM\JoinColumn(name="service_type_id", referencedColumnName="id")
     */
    private $serviceType;

    /**
     * @var integer
     * @ORM\Column(name="visibility", type="boolean", options={"default" : 1})
     */
    private $visibility = 1;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ServiceBundle\Entity\Service",
     * mappedBy="channelServiceType", cascade={"persist"})
     */
    private $service;


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
     * Set name
     *
     * @param string $name
     * @return ServiceGroup
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set visibility
     *
     * @param integer $visibility
     * @return ServiceGroup
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    
        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return ServiceGroup
     */
    public function setChannel(AccountChannel $channel = null)
    {
        $this->channel = $channel;
    
        return $this;
    }

    /**
     * Get channel
     *
     * @return \MFB\ChannelBundle\Entity\AccountChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set serviceType
     *
     * @param \MFB\ServiceBundle\Entity\ServiceType $serviceType
     * @return ChannelServiceType
     */
    public function setServiceType(\MFB\ServiceBundle\Entity\ServiceType $serviceType = null)
    {
        $this->serviceType = $serviceType;
    
        return $this;
    }

    /**
     * Get serviceType
     *
     * @return \MFB\ServiceBundle\Entity\ServiceType 
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->service = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add service
     *
     * @param \MFB\ServiceBundle\Entity\Service $service
     * @return ChannelServiceType
     */
    public function addService(\MFB\ServiceBundle\Entity\Service $service)
    {
        $this->service[] = $service;
    
        return $this;
    }

    /**
     * Remove service
     *
     * @param \MFB\ServiceBundle\Entity\Service $service
     */
    public function removeService(\MFB\ServiceBundle\Entity\Service $service)
    {
        $this->service->removeElement($service);
    }

    /**
     * Get service
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getService()
    {
        return $this->service;
    }
}