<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceTypeDefinition
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceTypeDefinition
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
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceType", inversedBy="definition")
     * @ORM\JoinColumn(name="service_type_id", referencedColumnName="id")
     */
    private $serviceType;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceDefinition")
     * @ORM\JoinColumn(name="service_definition_id", referencedColumnName="id")
     */
    private $serviceDefinition;



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
     * Set serviceType
     *
     * @param \MFB\ServiceBundle\Entity\ServiceType $serviceType
     * @return ServiceTypeDefinition
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
     * Set serviceDefinition
     *
     * @param \MFB\ServiceBundle\Entity\ServiceDefinition $serviceDefinition
     * @return ServiceTypeDefinition
     */
    public function setServiceDefinition(\MFB\ServiceBundle\Entity\ServiceDefinition $serviceDefinition = null)
    {
        $this->serviceDefinition = $serviceDefinition;
    
        return $this;
    }

    /**
     * Get serviceDefinition
     *
     * @return \MFB\ServiceBundle\Entity\ServiceDefinition 
     */
    public function getServiceDefinition()
    {
        return $this->serviceDefinition;
    }
}