<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MFB\ChannelBundle\Entity\AccountChannel;

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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ChannelBundle\Entity\AccountChannel", inversedBy="serviceGroup", cascade={"persist"})
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     **/
    private $channel;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     * @ORM\Column(name="visibility", type="boolean", options={"default" : 1})
     */
    private $visibility = 1;


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
     * Set visibility
     *
     * @param integer $visibility
     * @return ServiceGroup
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    
        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return ServiceGroup
     */
    public function setChannel(AccountChannel $channel = null)
    {
        $this->channel = $channel;
    
        return $this;
    }

    /**
     * Get channel
     *
     * @return \MFB\ChannelBundle\Entity\AccountChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }
}