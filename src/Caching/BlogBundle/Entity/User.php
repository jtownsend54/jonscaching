<?php
namespace Caching\BlogBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
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
    protected $username;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;
    
    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="User")
     */
    protected $Entries;
    
    /**
     * Returns the roles granted to the user.
     *
     * @return Role[] The user roles
     */
    function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * @return string The password
     */
    function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt.
     *
     * @return string The salt
     */
    function getSalt()
    {
        return 'cAc41ng_15=k3wL';
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * @return void
     */
    function eraseCredentials()
    {
        
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return Boolean
     */
    function equals(UserInterface $user)
    {
        return true;
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function __construct()
    {
        $this->Entries = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add Entries
     *
     * @param Caching\BlogBundle\Entity\Entry $entries
     */
    public function addEntry(\Caching\BlogBundle\Entity\Entry $entries)
    {
        $this->Entries[] = $entries;
    }

    /**
     * Get Entries
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEntries()
    {
        return $this->Entries;
    }
}