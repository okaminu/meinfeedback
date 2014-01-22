<?php

namespace MFB\DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MFB\DocumentBundle\DocumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
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
     * @ORM\Column(name="filetype", type="string", length=64)
     */
    private $filetype;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=64)
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\ManyToOne(
     * targetEntity="MFB\ChannelBundle\Entity\AccountChannel",
     * inversedBy="document",
     * cascade={"persist"})
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     */
    private $channel;

    private $file;

    private $extensionWhitelist = array('jpg', 'png');

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
     * @param mixed $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $file = $this->getFile();
        if (isset($file)) {
            $this->filename = sha1(uniqid(mt_rand(), true)) . '.'. $file->guessExtension();
            if (!in_array($file->guessExtension(), $this->extensionWhitelist)) {
                throw new DocumentException('Not allowed file extension');
            }
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        $file = $this->getFile();
        if (is_null($file)) {
            return $file;
        }
        if (!is_dir($this->getUserUploadRootDir())) {
            mkdir($this->getUserUploadRootDir(), 0755, true);
        }
        
        $file->move($this->getuserUploadRootDir(), $this->filename);
    }


    /**
     * Set filetype
     *
     * @param string $filetype
     * @return Document
     */
    public function setFiletype($filetype)
    {
        $this->filetype = $filetype;
    
        return $this;
    }

    /**
     * Get filetype
     *
     * @return string 
     */
    public function getFiletype()
    {
        return $this->filetype;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Document
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Document
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set channel
     *
     * @param \MFB\ChannelBundle\Entity\AccountChannel $channel
     * @return Document
     */
    public function setChannel(\MFB\ChannelBundle\Entity\AccountChannel $channel = null)
    {
        $this->channel = $channel;
    
        return $this;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getUserUploadRootDir() .'/'.$this->filename) {
            unlink($file);
        }
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

    public function getUploadDir()
    {
        return 'uploads';
    }

    public function getUserUploadRootDir()
    {
        $segments = array(
            __DIR__,
            '../Resources/public',
            $this->getUploadDir(),
            $this->getUserDir()
        );

        return implode('/', $segments);
    }

    public function getUserDir()
    {
        $segments =array(
            $this->filetype,
            $this->category,
            $this->channel->getAccountId(),
            $this->channel->getId());

        return implode('/', $segments);
    }

    public function getWebPath()
    {
        $segments = array(
            $this->getUploadDir(),
            $this->getUserDir(),
            $this->filename
        );

        return implode('/', $segments);
    }

}
