<?php

namespace MFB\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Rating
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
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_custom", type="boolean", options={"default" : 0})
     */
    private $isCustom = 0;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ServiceBundle\Entity\ServiceTypeCriteria", mappedBy="rating")
     */
    private $serviceTypeCriteria;


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
     * @return Rating
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
     * Constructor
     */
    public function __construct()
    {
        $this->serviceTypeCriteria = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add serviceTypeCriteria
     *
     * @param \MFB\ServiceBundle\Entity\ServiceTypeCriteria $serviceTypeCriteria
     * @return Rating
     */
    public function addServiceTypeCriteria(\MFB\ServiceBundle\Entity\ServiceTypeCriteria $serviceTypeCriteria)
    {
        $this->serviceTypeCriteria[] = $serviceTypeCriteria;
    
        return $this;
    }

    /**
     * Remove serviceTypeCriteria
     *
     * @param \MFB\ServiceBundle\Entity\ServiceTypeCriteria $serviceTypeCriteria
     */
    public function removeServiceTypeCriteria(\MFB\ServiceBundle\Entity\ServiceTypeCriteria $serviceTypeCriteria)
    {
        $this->serviceTypeCriteria->removeElement($serviceTypeCriteria);
    }

    /**
     * Get serviceTypeCriteria
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiceTypeCriteria()
    {
        return $this->serviceTypeCriteria;
    }

    /**
     * Set isCustom
     *
     * @param boolean $isCustom
     * @return Rating
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
}