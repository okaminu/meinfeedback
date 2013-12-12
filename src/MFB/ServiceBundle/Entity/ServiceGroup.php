<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceType
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServiceGroup
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * @return ServiceGroup
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
     * Set channelId
     *
     * @param int
     * @return ServiceGroup
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    
        return $this;
    }

    /**
     * Get channelId
     *
     * @return \MFB\ChannelBundle\Entity\AccountChannel 
     */
    public function getChannelId()
    {
        return $this->channelId;
    }
}