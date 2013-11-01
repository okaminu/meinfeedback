<?php

namespace MFB\EmailBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmailTemplate
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EmailTemplate
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=16)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="template_code", type="text")
     */
    private $templateCode;

    /**
     * @var string
     *
     * @ORM\Column(name="thank_you_code", type="text")
     */
    private $thankYouCode;

    /**
     * @ORM\OneToMany(targetEntity="EmailTemplateVariable", mappedBy="emailTemplate",cascade={"persist"})
     **/
    private $variables;

    public function __construct()
    {
        $this->variables = new ArrayCollection();
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
     * @return EmailTemplate
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
     * @return EmailTemplate
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
     * Set title
     *
     * @param string $title
     * @return EmailTemplate
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set templateCode
     *
     * @param string $templateCode
     * @return EmailTemplate
     */
    public function setTemplateCode($templateCode)
    {
        $this->templateCode = $templateCode;
    
        return $this;
    }

    /**
     * Get templateCode
     *
     * @return string 
     */
    public function getTemplateCode()
    {
        return $this->templateCode;
    }

    /**
     * @param string $thankYouCode
     */
    public function setThankYouCode($thankYouCode)
    {
        $this->thankYouCode = $thankYouCode;
    }

    /**
     * @return string
     */
    public function getThankYouCode()
    {
        return $this->thankYouCode;
    }

    /**
     * @param ArrayCollection $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return ArrayCollection|EmailTemplateVariable[]
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param EmailTemplateVariable $variable
     */
    public function addVariable($variable)
    {
        $this->variables->add($variable);
    }
}
