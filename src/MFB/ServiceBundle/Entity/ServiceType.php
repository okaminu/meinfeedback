<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceType
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceType
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\Business", inversedBy="serviceType", cascade={"persist"})
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ChannelBundle\Entity\ChannelServiceType",
     * mappedBy="serviceType", cascade={"persist"})
     */
    private $channelServiceType;


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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set business
     *
     * @param \MFB\ServiceBundle\Entity\Bussiness $business
     * @return ServiceType
     */
    public function setBusiness(\MFB\ServiceBundle\Entity\Bussiness $business = null)
    {
        $this->business = $business;
    
        return $this;
    }

    /**
     * Get business
     *
     * @return \MFB\ServiceBundle\Entity\Bussiness 
     */
    public function getBusiness()
    {
        return $this->business;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->channelServiceType = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add channelServiceType
     *
     * @param \MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType
     * @return ServiceType
     */
    public function addChannelServiceType(\MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType)
    {
        $this->channelServiceType[] = $channelServiceType;
    
        return $this;
    }

    /**
     * Remove channelServiceType
     *
     * @param \MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType
     */
    public function removeChannelServiceType(\MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType)
    {
        $this->channelServiceType->removeElement($channelServiceType);
    }

    /**
     * Get channelServiceType
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChannelServiceType()
    {
        return $this->channelServiceType;
    }
}