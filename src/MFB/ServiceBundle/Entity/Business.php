<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Business
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Business
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
     *
     * @ORM\OneToMany(targetEntity="MFB\ServiceBundle\Entity\ServiceType", mappedBy="business", cascade={"persist"})
     */
    private $serviceType;


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
     * Constructor
     */
    public function __construct()
    {
        $this->serviceType = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add serviceType
     *
     * @param \MFB\ServiceBundle\Entity\ServiceType $serviceType
     * @return Business
     */
    public function addServiceType(\MFB\ServiceBundle\Entity\ServiceType $serviceType)
    {
        $this->serviceType[] = $serviceType;
    
        return $this;
    }

    /**
     * Remove serviceType
     *
     * @param \MFB\ServiceBundle\Entity\ServiceType $serviceType
     */
    public function removeServiceType(\MFB\ServiceBundle\Entity\ServiceType $serviceType)
    {
        $this->serviceType->removeElement($serviceType);
    }

    /**
     * Get serviceType
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }
}