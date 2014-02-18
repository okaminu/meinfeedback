<?php

namespace MFB\SetupWizardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WizardStep
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WizardStep
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
     * @ORM\ManyToOne(
     * targetEntity="MFB\ChannelBundle\Entity\AccountChannel")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    /**
     * @var string
     * @ORM\Column(name="route", type="string", length=128)
     */
    private $route;


    /**
     * @var integer
     * @ORM\Column(name="status", type="integer")
     *
     */
    private $status;


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
     * Set route
     *
     * @param string $route
     * @return WizardStep
     */
    public function setRoute($route)
    {
        $this->route = $route;
    
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return WizardStep
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return WizardStep
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