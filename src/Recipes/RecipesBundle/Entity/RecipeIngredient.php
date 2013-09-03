<?php

namespace Recipes\RecipesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ingredient
 *
 * @ORM\Table(name="Recipe_Ingredient")
 * @ORM\Entity
 */
class RecipeIngredient
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeIngredient")
     */
    private $recipe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ingredient", inversedBy="recipeIngredient")
     */
    private $ingredient;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="string", length=64)
     */
    private $count;

    /**
     * @var \MeasureUnit
     *
     * @ORM\ManyToOne(targetEntity="MeasureUnit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="measure_unit_id", referencedColumnName="id")
     * })
     */
    private $measureUnit;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipe = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set recipe
     *
     * @param \Recipes\RecipesBundle\Entity\Recipe $recipe
     * @return RecipeIngredient
     */
    public function setRecipe(\Recipes\RecipesBundle\Entity\Recipe $recipe = null)
    {
        $this->recipe = $recipe;
    
        return $this;
    }

    /**
     * Get recipe
     *
     * @return \Recipes\RecipesBundle\Entity\Recipe 
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set name
     *
     * @param string $count
     * @return RecipeIngredient
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return string
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set ingredient
     *
     * @param \Recipes\RecipesBundle\Entity\Ingredient $ingredient
     * @return RecipeIngredient
     */
    public function setIngredient(\Recipes\RecipesBundle\Entity\Ingredient $ingredient = null)
    {
        $this->ingredient = $ingredient;
    
        return $this;
    }

    /**
     * Get ingredient
     *
     * @return \Recipes\RecipesBundle\Entity\Ingredient 
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * Set measureUnit
     *
     * @param \Recipes\RecipesBundle\Entity\MeasureUnit $measureUnit
     * @return Ingredient
     */
    public function setMeasureUnit(\Recipes\RecipesBundle\Entity\MeasureUnit $measureUnit = null)
    {
        $this->measureUnit = $measureUnit;

        return $this;
    }

    /**
     * Get measureUnit
     *
     * @return \Recipes\RecipesBundle\Entity\MeasureUnit
     */
    public function getMeasureUnit()
    {
        return $this->measureUnit;
    }
}