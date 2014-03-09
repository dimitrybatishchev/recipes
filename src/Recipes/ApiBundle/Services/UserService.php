<?php

namespace Recipes\ApiBundle\Services;

class UserService {
    protected $uid;
    protected $favoritesRecipes;

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function getFavoritesRecipes()
    {
        return $this->favoritesRecipes;
    }

    public function setFavoritesRecipes($favoritesRecipes)
    {
        $this->favoritesRecipes = $favoritesRecipes;
    }

    public function existsInFavoritesRecipes($recipeId)
    {
        foreach ($this->favoritesRecipes as $recipe){
            if ($recipe->getId() == $recipeId){
                return true;
            }
        }
        return false;
    }
}