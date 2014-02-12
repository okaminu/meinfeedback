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
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ServiceBundle\Entity\ServiceTypeDefinition",
     * mappedBy="serviceType", cascade={"persist"})
     */
    private $serviceTypeDefinition;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_custom", type="boolean", options={"default" : 0})
     */
    private $isCustom = 0;


    public function __construct()
    {
        $this->channelServiceType = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @param \MFB\ServiceBundle\Entity\Business $business
     * @return ServiceType
     */
    public function setBusiness(\MFB\ServiceBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }
    /**
     * Get business
     *
     * @return \MFB\ServiceBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
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

    /**
     * Set isCustom
     *
     * @param boolean $isCustom
     * @return ServiceType
     */
    public function setIsCustom($isCustom)
    {
        $this->isCustom = $isCustom;
    
        return $this;
    }

    /**
     * Get isCustom
     *
     * @return boolean 
     */
    public function getIsCustom()
    {
        return $this->isCustom;
    }

    /**
     * Add serviceTypeDefinition
     *
     * @param \MFB\ServiceBundle\Entity\ServiceTypeDefinition $serviceTypeDefinition
     * @return ServiceType
     */
    public function addServiceTypeDefinition(\MFB\ServiceBundle\Entity\ServiceTypeDefinition $serviceTypeDefinition)
    {
        $this->serviceTypeDefinition[] = $serviceTypeDefinition;
    
        return $this;
    }

    /**
     * Remove serviceTypeDefinition
     *
     * @param \MFB\ServiceBundle\Entity\ServiceTypeDefinition $serviceTypeDefinition
     */
    public function removeServiceTypeDefinition(\MFB\ServiceBundle\Entity\ServiceTypeDefinition $serviceTypeDefinition)
    {
        $this->serviceTypeDefinition->removeElement($serviceTypeDefinition);
    }

    /**
     * Get serviceTypeDefinition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiceTypeDefinition()
    {
        return $this->serviceTypeDefinition;
    }

    public function getDefinitions()
    {
        $servicedDefinitions = $this->getServiceTypeDefinition();
        $definitions = array();
        foreach ($servicedDefinitions as $single) {
            $definitions[$single->getServiceDefinition()->getName()] = $single->getServiceDefinition();
        }
        return $definitions;
    }
}