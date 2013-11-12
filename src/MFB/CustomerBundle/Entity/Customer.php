<?php

namespace MFB\CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="email_per_account", columns={"account_id", "email"})})
 * @ORM\Entity
 */
class Customer
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
     * @ORM\Column(name="email", type="string", length=128)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="integer", nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=32, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=32, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="salutation", type="string", length=16, nullable=true)
     */
    private $salutation;

    /**
     * @var string
     *
     * @ORM\Column(name="service_date", type="date", nullable=true)
     */
    private $serviceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_id", type="string", length=16, nullable=true)
     */
    private $referenceId;

    /**
     * @var string
     *
     * @ORM\Column(name="homepage", type="string", length=128, nullable=true)
     */
    private $homepage;


    /**
     * @var string
     *
     * @ORM\Column(name="serviceDescription", type="string", length=255, nullable=true)
     */
    private $serviceDescription;

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
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     * @return Customer
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
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }


    /**
     * Set serviceDate
     *
     * @param \DateTime $serviceDate
     * @return Customer
     */
    public function setServiceDate($serviceDate)
    {
        $this->serviceDate = $serviceDate;
    
        return $this;
    }

    /**
     * Get serviceDate
     *
     * @return \DateTime 
     */
    public function getServiceDate()
    {
        return $this->serviceDate;
    }

    /**
     * Set referenceId
     *
     * @param string $referenceId
     * @return Customer
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    
        return $this;
    }

    /**
     * Get referenceId
     *
     * @return string 
     */
    public function getReferenceId()
    {
        return $this->referenceId;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Customer
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set serviceDescription
     *
     * @param string $serviceDescription
     * @return Customer
     */
    public function setServiceDescription($serviceDescription)
    {
        $this->serviceDescription = $serviceDescription;
    
        return $this;
    }

    /**
     * Get serviceDescription
     *
     * @return string 
     */
    public function getServiceDescription()
    {
        return $this->serviceDescription;
    }
}