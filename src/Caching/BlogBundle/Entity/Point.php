<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Caching\BlogBundle\Entity\Route as Route;

/**
 * @ORM\Entity
 */
class Point
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    protected $longitude;
    
    /**
     * @ORM\ManyToOne(targetEntity="Route", inversedBy="Points")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id")
     */
    protected $Route;

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
     * Set latitude
     *
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set long
     *
     * @param datetime $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return datetime 
     */
    public function getLongitude()
    {
        return $this->longitude;
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
     * Get Route
     *
     * @return Route 
     */
    public function getRoute()
    {
        return $this->Route;
    }
}