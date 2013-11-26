<?php

namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountChannel
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="MFB\ChannelBundle\Entity\AccountChannelManager")
 */
class AccountChannel
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
     * @ORM\Column(name="account_id", type="integer", unique=true, nullable=false)
     */
    private $accountId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="homepageUrl", type="string", length=128)
     */
    private $homepageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=128)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=128)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=128)
     */
    private $city;


    /**
     * @var smallint
     *
     * @ORM\Column(name="ratings_enabled", type="smallint")
     */
    private $ratingsEnabled = 0;

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
     * @return AccountChannel
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
     * Set name
     *
     * @param string $name
     * @return AccountChannel
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
     * Set ratingsEnabled
     *
     * @param integer $ratingsEnabled
     * @return AccountChannel
     */
    public function setRatingsEnabled($ratingsEnabled)
    {
        $this->ratingsEnabled = $ratingsEnabled;
    
        return $this;
    }

    /**
     * Get ratingsEnabled
     *
     * @return integer 
     */
    public function getRatingsEnabled()
    {
        return $this->ratingsEnabled;
    }

    /**
     * Set homepageUrl
     *
     * @param string $homepageUrl
     * @return AccountChannel
     */
    public function setHomepageUrl($homepageUrl)
    {
        $this->homepageUrl = $homepageUrl;
    
        return $this;
    }

    /**
     * Get homepageUrl
     *
     * @return string 
     */
    public function getHomepageUrl()
    {
        return $this->homepageUrl;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return AccountChannel
     */
    public function setStreet($street)
    {
        $this->street = $street;
    
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return AccountChannel
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return AccountChannel
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
}