<?php

namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChannelServiceDefinition
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="definition_per_channel", columns={"channel_id", "service_definition_id"})})
 * @ORM\Entity
 */
class ChannelServiceDefinition
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
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\ServiceDefinition")
     * @ORM\JoinColumn(name="service_definition_id", referencedColumnName="id")
     */
    private $serviceDefinition;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="MFB\ChannelBundle\Entity\AccountChannel",
     * inversedBy="serviceDefinition",
     * cascade={"persist"})
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;




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
     * Set serviceDefinition
     *
     * @param \MFB\ServiceBundle\Entity\ServiceDefinition $serviceDefinition
     * @return ChannelServiceDefinition
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

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return ChannelServiceDefinition
     */
    public function setChannel(\MFB\ChannelBundle\Entity\AccountChannel $channel = null)
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