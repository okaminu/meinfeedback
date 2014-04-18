<?php
namespace MFB\WidgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Widget
 * @ORM\Entity()
 * @ORM\Table()
 */
class Widget
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
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var integer
     *
     * @ORM\Column(name="channel_id", type="integer")
     */
    private $channelId;

    /**
     * @var string
     *
     * @ORM\Column(name="text_color", type="string", length=32, nullable=true)
     */
    private $textColorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="background_color", type="string", length=32, nullable=true)
     */
    private $backgroundColorCode;

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
     * @return Widget
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
     * Set channelId
     *
     * @param integer $channelId
     * @return Widget
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
     * Set textColorCode
     *
     * @param string $textColorCode
     * @return Widget
     */
    public function setTextColorCode($textColorCode)
    {
        $this->textColorCode = $textColorCode;
    
        return $this;
    }

    /**
     * Get textColorCode
     *
     * @return string 
     */
    public function getTextColorCode()
    {
        return $this->textColorCode;
    }

    /**
     * Set backgroundColorCode
     *
     * @param string $backgroundColorCode
     * @return Widget
     */
    public function setBackgroundColorCode($backgroundColorCode)
    {
        $this->backgroundColorCode = $backgroundColorCode;
    
        return $this;
    }

    /**
     * Get backgroundColorCode
     *
     * @return string 
     */
    public function getBackgroundColorCode()
    {
        return $this->backgroundColorCode;
    }
}