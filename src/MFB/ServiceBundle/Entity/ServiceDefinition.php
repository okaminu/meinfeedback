<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceDefinition
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceDefinition
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
     * @var boolean
     *
     * @ORM\Column(name="is_custom", type="boolean", options={"default" : 0})
     */
    private $isCustom = 0;


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
     * @return ServiceDefinition
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
     * Set isCustom
     *
     * @param boolean $isCustom
     * @return ServiceDefinition
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

    public function __toString()
    {
        return (string)$this->id;
    }

}