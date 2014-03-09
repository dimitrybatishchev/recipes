<?php

namespace Recipes\RecipesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\SerializerBundle\Annotation as JMS;
use JMS\SerializerBundle\Annotation\Groups;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\VirtualProperty;
use JMS\SerializerBundle\Annotation\SerializedName;

/**
 * Recipe
 *
 * @ORM\Table(name="Recipe")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Recipe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     * @SerializedName("id")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     * @JMS\Expose
     * @SerializedName("name")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     * @JMS\Expose
     * @SerializedName("description")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="RecipeIngredient", mappedBy="recipe")
     * @JMS\Expose
     * @SerializedName("recipeIngredient")
     */
    private $recipeIngredient;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     *
     * @JMS\Expose
     * @SerializedName("category")
     */
    private $category;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     *
     * @JMS\Expose
     * @SerializedName("creator")
     */
    private $creator;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="likedRecipes")
     * @ORM\JoinTable(name="User_Liked_Recipes")
     * @SerializedName("usersWhoLiked")
     */
    private $usersWhoLiked;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="recipe")
     * @JMS\Expose
     * @SerializedName("comments")
     */
    private $comments;

    /**
     * @var \Cuisine
     *
     * @ORM\ManyToOne(targetEntity="Cuisine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuisine_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     * @SerializedName("cuisine")
     */
    private $cuisine;

    /**
     * @ORM\Column(name="image",type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    public $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $favorite;

    /**
     * @VirtualProperty
     * @SerializedName("favorite")
     * @Groups({"manage"})
     */
    public function foo(){
        return $this->favorite;
    }

    public function setFavorite($favorite)
    {
        $this->favorite = $favorite;

        return $this;
    }



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ingredient = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Recipe
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
     * Set description
     *
     * @param string $description
     * @return Recipe
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add recipeIngredient
     *
     * @param \Recipes\RecipesBundle\Entity\RecipeIngredient $recipeIngredient
     * @return Recipe
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

    /**
     * Set category
     *
     * @param \Recipes\RecipesBundle\Entity\Category $category
     * @return Recipe
     */
    public function setCategory(\Recipes\RecipesBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return \Recipes\RecipesBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set creator
     *
     * @param \Recipes\RecipesBundle\Entity\User $creator
     * @return Recipe
     */
    public function setCreator(\Recipes\RecipesBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Recipes\RecipesBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set cuisine
     *
     * @param \Recipes\RecipesBundle\Entity\Cuisine $cuisine
     * @return Recipe
     */
    public function setCuisine(\Recipes\RecipesBundle\Entity\Cuisine $cuisine = null)
    {
        $this->cuisine = $cuisine;
    
        return $this;
    }

    /**
     * Get cuisine
     *
     * @return \Recipes\RecipesBundle\Entity\Cuisine 
     */
    public function getCuisine()
    {
        return $this->cuisine;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Recipe
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add usersWhoLiked
     *
     * @param \Recipes\RecipesBundle\Entity\User $usersWhoLiked
     * @return Recipe
     */
    public function addUsersWhoLiked(\Recipes\RecipesBundle\Entity\User $usersWhoLiked)
    {
        $this->usersWhoLiked[] = $usersWhoLiked;
    
        return $this;
    }

    /**
     * Remove usersWhoLiked
     *
     * @param \Recipes\RecipesBundle\Entity\User $usersWhoLiked
     */
    public function removeUsersWhoLiked(\Recipes\RecipesBundle\Entity\User $usersWhoLiked)
    {
        $this->usersWhoLiked->removeElement($usersWhoLiked);
    }

    /**
     * Get usersWhoLiked
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersWhoLiked()
    {
        return $this->usersWhoLiked;
    }

    /**
     * Get usersWhoLike
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersWhoLike()
    {
        return $this->usersWhoLike;
    }

    /**
     * Add comments
     *
     * @param \Recipes\RecipesBundle\Entity\Comment $comments
     * @return Recipe
     */
    public function addComment(\Recipes\RecipesBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Recipes\RecipesBundle\Entity\Comment $comments
     */
    public function removeComment(\Recipes\RecipesBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}