<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceTypeCriteria
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceTypeCriteria
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
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceType")
     * @ORM\JoinColumn(name="service_type_id", referencedColumnName="id")
     */
    private $serviceType;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\RatingBundle\Entity\Rating")
     * @ORM\JoinColumn(name="rating_id", referencedColumnName="id")
     */
    private $rating;

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
     * @return ServiceTypeCriteria
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
     * Set rating
     *
     * @param \MFB\RatingBundle\Entity\Rating $rating
     * @return ServiceTypeCriteria
     */
    public function setRating(\MFB\RatingBundle\Entity\Rating $rating = null)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return \MFB\RatingBundle\Entity\Rating 
     */
    public function getRating()
    {
        return $this->rating;
    }
}