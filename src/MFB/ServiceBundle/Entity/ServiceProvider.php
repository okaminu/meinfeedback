<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceProvider
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceProvider
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
     * @ORM\Column(name="channel_id", type="integer")
     */
    private $channelId;

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;


    /**
     * @var string
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    

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
     * Set channelId
     *
     * @param integer $channelId
     * @return ServiceProvider
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    
        return $this;
    }

    /**
     * Get channelId
     *
     * @return integer 
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return ServiceProvider
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return ServiceProvider
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}