<?php

namespace MFB\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MFB\EmailBundle\Entity\EmailTemplate;

/**
 * FeedbackInvite
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class FeedbackInvite
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
     * @ORM\Column(name="token", type="string", unique=true, length=32)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="customer_id", type="integer")
     */
    private $customerId;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="channel_id", type="integer")
     */
    private $channelId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var EmailTemplate
     *
     * @ORM\ManyToOne(targetEntity="\MFB\EmailBundle\Entity\EmailTemplate")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    private $emailTemplate;

    /**
     * @var $service
     * @ORM\ManyToOne(targetEntity="\MFB\ServiceBundle\Entity\Service", fetch="EAGER")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

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
     * Set token
     *
     * @param string $token
     * @return FeedbackInvite
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     * @return FeedbackInvite
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
     * Set customerId
     *
     * @param \stdClass $customerId
     * @return FeedbackInvite
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    
        return $this;
    }

    /**
     * Get customerId
     *
     * @return \stdClass 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param \stdClass $channelId
     * @return FeedbackInvite
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * Set created
     *
     * @param \DateTime $createdAt
     * @return FeedbackInvite
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function setEmailTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return EmailTemplate
     */
    public function getEmailTemplate()
    {
        return $this->emailTemplate;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     *
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }

        if ($this->getToken() == null) {
            $this->generateToken();
        }
    }

    public function generateToken()
    {
        $this->setToken(
            md5(
                implode(
                    '_',
                    array(
                        $this->getAccountId(),
                        $this->getCustomerId(),
                        $this->getCreatedAt()->format('Y-m-d')
                    )
                )
            )
        );
    }

    /**
     * Set service
     *
     * @param \MFB\ServiceBundle\Entity\Service $service
     * @return FeedbackInvite
     */
    public function setService(\MFB\ServiceBundle\Entity\Service $service = null)
    {
        $this->service = $service;
    
        return $this;
    }

    /**
     * Get service
     *
     * @return \MFB\ServiceBundle\Entity\Service 
     */
    public function getService()
    {
        return $this->service;
    }
}