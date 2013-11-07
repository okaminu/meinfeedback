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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

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
     * Set address
     *
     * @param string $address
     * @return AccountChannel
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
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
}