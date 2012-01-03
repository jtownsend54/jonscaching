<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Caching\BlogBundle\Entity\Point as Point;

/**
 * @ORM\Entity(repositoryClass="Caching\BlogBundle\Repository\RouteRepository")
 */
class Route
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="date")
     */
    protected $routeDate;
    
    /**
     * @ORM\OneToMany(targetEntity="Point", mappedBy="Route")
     */
    protected $Points;
    public function __construct()
    {
        $this->Points = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set routeDate
     *
     * @param datetime $routeDate
     */
    public function setRouteDate($routeDate)
    {
        $this->routeDate = $routeDate;
    }

    /**
     * Get routeDate
     *
     * @return datetime 
     */
    public function getRouteDate()
    {
        return $this->routeDate;
    }

    /**
     * Add Points
     *
     * @param Point $points
     */
    public function addPoint(Point $points)
    {
        $this->Points[] = $points;
    }

    /**
     * Get Points
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->Points;
    }
}