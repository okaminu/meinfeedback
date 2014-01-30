<?php

namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountChannel
 *
 * @ORM\Table()
 * @ORM\Entity
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
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="homepageUrl", type="string", length=128)
     */
    private $homepageUrl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=128)
     */
    private $street = '';

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=128)
     */
    private $place = '';

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=128)
     */
    private $city = '';

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\CountryBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     **/
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=128, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ServiceBundle\Entity\ServiceProvider", mappedBy="channel", cascade={"persist"})
     **/
    private $serviceProvider;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\ChannelBundle\Entity\ChannelServiceType",
     * mappedBy="channel", cascade={"persist"})
     **/
    private $channelServiceType;

    /**
     * @var integer
     *
     * @ORM\OneToMany(
     * targetEntity="MFB\ChannelBundle\Entity\ChannelRatingCriteria",
     * mappedBy="channel",
     * cascade={"persist"}
     * )
     */
    private $ratingCriteria;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\DocumentBundle\Entity\Document", mappedBy="channel", cascade={"persist"})
     **/
    private $document;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\Business", cascade={"persist"})
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serviceProvider = new \Doctrine\Common\Collections\ArrayCollection();
        $this->serviceGroup = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set country
     *
     * @param \MFB\CountryBundle\Entity\Country $country
     * @return AccountChannel
     */
    public function setCountry(\MFB\CountryBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \MFB\CountryBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return AccountChannel
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    
        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    
    /**
     * Add serviceProvider
     *
     * @param \MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider
     * @return AccountChannel
     */
    public function addServiceProvider(\MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider)
    {
        $this->serviceProvider[] = $serviceProvider;
    
        return $this;
    }

    /**
     * Remove serviceProvider
     *
     * @param \MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider
     */
    public function removeServiceProvider(\MFB\ServiceBundle\Entity\ServiceProvider $serviceProvider)
    {
        $this->serviceProvider->removeElement($serviceProvider);
    }

    /**
     * Get serviceProvider
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiceProvider()
    {
        return $this->serviceProvider;
    }

    /**
     * Get serviceGroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiceGroup()
    {
        return $this->serviceGroup;
    }

    /**
     * Add ratingCriteria
     *
     * @param \MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria
     * @return AccountChannel
     */
    public function addRatingCriteria(\MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria)
    {
        $this->ratingCriteria[] = $ratingCriteria;
    
        return $this;
    }

    /**
     * Remove ratingCriteria
     *
     * @param \MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria
     */
    public function removeRatingCriteria(\MFB\ChannelBundle\Entity\ChannelRatingCriteria $ratingCriteria)
    {
        $this->ratingCriteria->removeElement($ratingCriteria);
    }

    /**
     * Get ratingCriteria
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRatingCriteria()
    {
        return $this->ratingCriteria;
    }

    /**
     * Add document
     *
     * @param \MFB\DocumentBundle\Entity\Document $document
     * @return AccountChannel
     */
    public function addDocument(\MFB\DocumentBundle\Entity\Document $document)
    {
        $this->document[] = $document;
    
        return $this;
    }

    /**
     * Remove document
     *
     * @param \MFB\DocumentBundle\Entity\Document $document
     */
    public function removeDocument(\MFB\DocumentBundle\Entity\Document $document)
    {
        $this->document->removeElement($document);
    }

    /**
     * Get document
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Add channelServiceType
     *
     * @param \MFB\ChannelBundle\Entity\ChannelServiceType $channelServiceType
     * @return AccountChannel
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
     * Set business
     *
     * @param \MFB\ServiceBundle\Entity\Business $business
     * @return AccountChannel
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
}