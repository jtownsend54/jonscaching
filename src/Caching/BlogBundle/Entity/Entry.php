<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Caching\BlogBundle\Entity\User;
use Caching\BlogBundle\Entity\Route;

/**
 * @ORM\Entity
 */
class Entry
{
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="Entries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $User;

    /**
     * @ORM\ManyToMany(targetEntity="Route", inversedBy="Entries")
     * @ORM\JoinTable(name="entries_routes")
     */
    private $Routes;

    public function __construct()
    {
        $this->Routes = new ArrayCollection();
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
     * Add Routes
     *
     * @param Route $routes
     */
    public function addRoute(\Caching\BlogBundle\Entity\Route $routes)
    {
        $this->Routes[] = $routes;
    }

    /**
     * Get Routes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRoutes()
    {
        return $this->Routes;
    }
}