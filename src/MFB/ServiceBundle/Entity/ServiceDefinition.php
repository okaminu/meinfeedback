<?php

namespace MFB\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceDefinition
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="definition_per_channel", columns={"channel_id", "definition"})})
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
     * @ORM\Column(name="definition", type="string", length=255)
     */
    private $definition;

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
     * Set definition
     *
     * @param string $definition
     * @return ChannelServiceDefinition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    
        return $this;
    }

    /**
     * Get definition
     *
     * @return string 
     */
    public function getDefinition()
    {
        return $this->definition;
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