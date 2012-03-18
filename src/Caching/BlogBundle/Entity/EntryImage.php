<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Caching\BlogBundle\Entity\Entry;

/**
 * @ORM\Entity
 * @ORM\Table(name="entry_image")
 */
class EntryImage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", name="full_path")
     */
    protected $fullPath;
    
    /**
     * @ORM\Column(type="string", name="thumb_path")
     */
    protected $thumbPath;

    /**
     * @ORM\ManyToOne(targetEntity="Entry", inversedBy="EntryImages", cascade={"persist"})
     * @ORM\JoinColumn(name="entry_id", referencedColumnName="id")
     */
    protected $Entry;

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
     * Set fullPath
     *
     * @param string $fullPath
     */
    public function setFullPath($fullPath)
    {
        $this->fullPath = $fullPath;
    }

    /**
     * Get fullPath
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * Set thumbPath
     *
     * @param string $thumbPath
     */
    public function setThumbPath($thumbPath)
    {
        $this->thumbPath = $thumbPath;
    }

    /**
     * Get thumbPath
     *
     * @return string 
     */
    public function getThumbPath()
    {
        return $this->thumbPath;
    }

    /**
     * Set Entry
     *
     * @param Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->Entry = $entry;
    }

    /**
     * Get Entry
     *
     * @return string
     */
    public function getEntry()
    {
        return $this->Entry;
    }
}