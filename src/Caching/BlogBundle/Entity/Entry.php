<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Caching\BlogBundle\Entity\User;
use Caching\BlogBundle\Entity\Route;
use Caching\BlogBundle\Entity\EntryImage;

/**
 * @ORM\Entity(repositoryClass="Caching\BlogBundle\Repository\EntryRepository")
 * @ORM\Table(name="entry")
 */
class Entry
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $entry;

    /**
     * @ORM\Column(type="integer")
     */
    protected $active;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="Entries", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $User;

    /**
     * @ORM\OneToOne(targetEntity="Route", inversedBy="Entry")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id")
     */
    protected $Route;

    /**
     * @ORM\OneToMany(targetEntity="EntryImage", mappedBy="Entry")
     */
    protected $EntryImages;

    public function __construct()
    {
        $this->EntryImages = new ArrayCollection();
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
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set entry
     *
     * @param text $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the active state of an entry
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * Set the active state of an entry
     *
     * @param integer $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get entry
     *
     * @return text
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Set User
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->User = $user;
    }

    /**
     * Get User
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->User;
    }

    /**
     * Get Route
     *
     * @return Collection
     */
    public function getRoute()
    {
        return $this->Route;
    }

    /**
     * Set Route
     *
     * @param Route $route
     */
    public function setRoute(Route $route)
    {
        $this->Route = $route;
    }

    /**
     * Get Images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->EntryImages;
    }

    /**
     * Add Image
     *
     * @param EntryImage $image
     */
    public function addImage(EntryImage $image)
    {
        $this->EntryImages[] = $image;
    }
}