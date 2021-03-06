<?php

namespace Recipes\RecipesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\SerializerBundle\Annotation as JMS;

/**
 * Ingredient
 *
 * @ORM\Table(name="Ingredient")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Ingredient
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="ingredient")
     *
     */
    private $recipeIngredient;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipe = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Cuisine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Add recipeIngredient
     *
     * @param \Recipes\RecipesBundle\Entity\RecipeIngredient $recipeIngredient
     * @return Ingredient
     */
    public function addRecipeIngredient(\Recipes\RecipesBundle\Entity\RecipeIngredient $recipeIngredient)
    {
        $this->recipeIngredient[] = $recipeIngredient;
    
        return $this;
    }

    /**
     * Remove recipeIngredient
     *
     * @param \Recipes\RecipesBundle\Entity\RecipeIngredient $recipeIngredient
     */
    public function removeRecipeIngredient(\Recipes\RecipesBundle\Entity\RecipeIngredient $recipeIngredient)
    {
        $this->recipeIngredient->removeElement($recipeIngredient);
    }

    /**
     * Get recipeIngredient
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipeIngredient()
    {
        return $this->recipeIngredient;
    }

}