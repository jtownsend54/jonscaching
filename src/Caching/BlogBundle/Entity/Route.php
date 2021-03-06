<?php
namespace Caching\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Caching\BlogBundle\Entity\Point;
use Caching\BlogBundle\Entity\Entry;

/**
 * @ORM\Entity(repositoryClass="Caching\BlogBundle\Repository\RouteRepository")
 * @ORM\Table(name="route")
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
    protected $area;
    
    /**
     * @ORM\OneToMany(targetEntity="Point", mappedBy="Route")
     */
    protected $Points;

    /**
     * @ORM\OneToOne(targetEntity="Entry", mappedBy="Route")
     */
    protected $Entry;

    public function __construct()
    {
        $this->Points   = new ArrayCollection();
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
     * @return Collection
     */
    public function getPoints()
    {
        return $this->Points;
    }

    public function hasPoints()
    {
        return $this->Points->count() > 0;
    }

    /**
     * Set area
     *
     * @param string $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * Get area
     *
     * @return string 
     */
    public function getArea()
    {
        return $this->area;
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
     * @return Entry
     */
    public function getEntry()
    {
        return $this->Entry;
    }

    /**
     * Return the area name with lowercase letters and underscores instead of spaces
     *
     * @return string
     */
    public function getFolderName() {
        return strtolower(str_replace(' ', '_', $this->area));
    }
}