<?php

namespace Recipes\RecipesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\SerializerBundle\Annotation as JMS;

/**
 * Category
 *
 * @ORM\Table(name="User")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=128, nullable=false)
     * @JMS\Expose
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=128, nullable=false)
     * @JMS\Expose
     */
    private $lastname;

    /**
     * @ORM\Column(name="avatar",type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    public $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="Recipe", inversedBy="usersWhoLiked")
     */
    private $likedRecipes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->likedRecipes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get uid
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add likedRecipes
     *
     * @param \Recipes\RecipesBundle\Entity\Recipe $likedRecipes
     * @return User
     */
    public function addLikedRecipe(\Recipes\RecipesBundle\Entity\Recipe $likedRecipes)
    {
        $this->likedRecipes[] = $likedRecipes;
    
        return $this;
    }

    /**
     * Remove likedRecipes
     *
     * @param \Recipes\RecipesBundle\Entity\Recipe $likedRecipes
     */
    public function removeLikedRecipe(\Recipes\RecipesBundle\Entity\Recipe $likedRecipes)
    {
        $this->likedRecipes->removeElement($likedRecipes);
    }

    /**
     * Get likedRecipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikedRecipes()
    {
        return $this->likedRecipes;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

}