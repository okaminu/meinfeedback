<?php

namespace MFB\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MFB\EmailBundle\Entity\EmailTemplate;

/**
 * Feedback
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="\MFB\FeedbackBundle\Entity\FeedbackRepository")
 *
 */
class Feedback
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\CustomerBundle\Entity\Customer", cascade={"persist"})
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     **/
    private $customer;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MFB\ServiceBundle\Entity\Service", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     **/
    private $service;

    /**
     * @var integer
     *
     * @ORM\Column(name="channel_id", type="integer")
     */
    private $channelId;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content = null;

    /**
     * @var EmailTemplate
     *
     * @ORM\ManyToOne(targetEntity="\MFB\EmailBundle\Entity\EmailTemplate")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    private $emailTemplate;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_enabled", type="integer", nullable=false, options={"default" : 0})
     */
    private $isEnabled = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort = null;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="MFB\FeedbackBundle\Entity\FeedbackRating", mappedBy="feedback", cascade={"persist"})
     */
    private $feedbackRating;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->feedbackRating = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Feedback
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Feedback
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set channelId
     *
     * @param integer $channelId
     * @return Feedback
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
     * Set content
     *
     * @param string $content
     * @return Feedback
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
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
     *
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     * Set customer
     *
     * @param \MFB\CustomerBundle\Entity\Customer $customer
     * @return Feedback
     */
    public function setCustomer(\MFB\CustomerBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \MFB\CustomerBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param int $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return int
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * Set service
     *
     * @param \MFB\ServiceBundle\Entity\Service $service
     * @return Feedback
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

    /**
     * Add feedbackRating
     *
     * @param \MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating
     * @return Feedback
     */
    public function addFeedbackRating(\MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating)
    {
        $this->feedbackRating[] = $feedbackRating;
    
        return $this;
    }

    /**
     * Remove feedbackRating
     *
     * @param \MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating
     */
    public function removeFeedbackRating(\MFB\FeedbackBundle\Entity\FeedbackRating $feedbackRating)
    {
        $this->feedbackRating->removeElement($feedbackRating);
    }

    /**
     * Get feedbackRating
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedbackRating()
    {
        return $this->feedbackRating;
    }
}