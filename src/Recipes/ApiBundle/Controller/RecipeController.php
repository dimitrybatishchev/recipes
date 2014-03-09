<?php

namespace Recipes\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Recipes\RecipesBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Recipes\RecipesBundle\Entity\Recipe;
use Recipes\RecipesBundle\Entity\Category;
use Recipes\RecipesBundle\Entity\Cuisine;
use Recipes\RecipesBundle\Entity\Ingredient;
use Recipes\RecipesBundle\Entity\RecipeIngredient;
use Recipes\RecipesBundle\Entity\User;
use Recipes\RecipesBundle\Entity\MeasureUnit;
use Recipes\RecipesBundle\Entity\Comment;

class RecipeController extends Controller
{
    /**
     * @Route("/recipes/")
     * @Method("GET")
     * @Rest\View
     */
    public function getRecipesAction()
    {
        $request = $this->getRequest();
        $page = $request->query->get('page');
        $count = $request->query->get('count');
        $orderBy = $request->query->get('orderBy');
        $selectedIngredients = $request->query->get('ingredients');
        $selectedCategoryId = $request->query->get('category');
        $selectedCuisineId = $request->query->get('cuisine');
        $userId = $request->query->get('user');


        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder('Recipes')
            ->select('r')
            ->from('RecipesBundle:Recipe', 'r');



        if ($selectedCuisineId){
            $qb = $qb->leftJoin('r.cuisine', 'cuisine');
        }
        if ($selectedCategoryId){
            $qb = $qb->leftJoin('r.category', 'category');
        }
        if ($selectedCategoryId && $selectedCuisineId){
            $qb = $qb->where('cuisine.id = :cuisineId');
            $qb = $qb->andWhere('category.id = :categoryId');
        } elseif ($selectedCategoryId){
            $qb = $qb->where('category.id = :categoryId');
        } elseif ($selectedCuisineId) {
            $qb = $qb->where('cuisine.id = :cuisineId');
        }
        if ($selectedCuisineId){
            $qb = $qb->setParameter('cuisineId', $selectedCuisineId);
        }
        if ($selectedCategoryId){
            $qb = $qb->setParameter('categoryId', $selectedCategoryId);
        }

        if ($orderBy){
            $qb = $qb->orderBy('r.id', $orderBy);
        }
        if ($count){
            $qb = $qb->setMaxResults($count);
        }
        if ($page){
            $qb = $qb->setFirstResult($page*5);
        }


        $query = $qb->getQuery();
        $recipes = $query->getResult();

        if ($userId && $userId !== 'null'){
            $user = $em->getRepository('RecipesBundle:User')->find($userId);
            foreach ($user->getLikedRecipes() as $userRecipe){
                foreach ($recipes as $recipe){
                    if ($recipe->getId() == $userRecipe->getId()){
                        $userRecipe->setFavorite(true);
                    }
                }
            }
        }

        $matchedRecipes = array();

        if ($selectedIngredients){
            $selectedIngredients = explode(',', $selectedIngredients);
            $requiredIngredientsCount = count($selectedIngredients);
            foreach ($recipes as $recipe){
                $matched = 0;
                foreach($recipe->getRecipeIngredient() as $recipeIngredient){
                    $ingredientName = $recipeIngredient->getIngredient()->getName();
                    foreach($selectedIngredients as $requiredIngredient){
                        if ($ingredientName === $requiredIngredient){
                            $matched++;
                        }
                    }
                }
                if ($matched === $requiredIngredientsCount){
                    array_push($matchedRecipes, $recipe);
                }
            }
        } else {
            $matchedRecipes = $recipes;
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($recipes)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/recipes/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getRecipeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('RecipesBundle:Recipe')->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        } else {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($recipe)
                ->setFormat('json');
            return $this->get('fos_rest.view_handler')->handle($view);
        }
    }

    /**
     * @Route("/recipes/{recipeId}/comments")
     * @Method("POST")
     * @Template()
     */
    public function addCommentAction($recipeId)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('RecipesBundle:Recipe')->find($recipeId);

        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $userId = $json->{'userId'};

        $user = $em->getRepository('RecipesBundle:User')->find($userId);

        $comment = new Comment();
        $comment->setCreator($user);
        $comment->setRecipe($recipe);
        $comment->setText($json->{'text'});
        $comment->setCreated(new \DateTime("now"));

        $em->persist($comment);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/recipes/{id}")
     * @Method("POST")
     * @Template()
     */
    public function editRecipeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('RecipesBundle:Recipe')->find($id);

        $request = $this->getRequest();
        $json = json_decode($request->request->get('model'));

        $creatorUid = $json->{'creator'};

        if ($creatorUid != $recipe->getCreator()->getId()){
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        $em->getConnection()->beginTransaction();
        try {
            $recipe->setName($json->{'name'});
            $recipe->setDescription($json->{'description'});

            $cuisine = null;
            if (isset($json->{'cuisine'})){
                $cuisine = $em->getRepository('RecipesBundle:Cuisine')->findOneBy(array('name' => $json->{'cuisine'}));
            }
            $category = $em->getRepository('RecipesBundle:Category')->findOneBy(array('name' => $json->{'category'}));

            if (!$cuisine){
                $cuisine = new Cuisine();
                $cuisine->setName($json->{'cuisine'});
                $em->persist($cuisine);
                $em->flush();
            }
            if (!$category) {
                $category = new Category();
                $category->setName($json->{'category'});
                $em->persist($category);
                $em->flush();
            }

            $recipe->setCategory($category);
            $recipe->setCuisine($cuisine);


            $recipeIngredients = $recipe->getRecipeIngredient();

            // изменяем уже существующие ингоедиенты
            foreach($recipeIngredients as $currentRecipeIngredient){
                $exist = false;
                foreach ($json->{'ingredients'} as $ingredientJson) {
                    if (isset($ingredientJson->{'name'}) && isset($ingredientJson->{'count'})){
                        if ($ingredientJson->{'name'} == $currentRecipeIngredient->getIngredient()->getName()){
                            $currentRecipeIngredient->setCount($ingredientJson->{'count'});
                            $measureUnit = $em->getRepository('RecipesBundle:MeasureUnit')->find($ingredientJson->{'measureUnit'});
                            $currentRecipeIngredient->setMeasureUnit($measureUnit);
                            $em->persist($currentRecipeIngredient);
                            $em->flush();
                            $exist = true;
                        }
                    }
                }
            }

            // добавляем не существующие
            foreach ($json->{'ingredients'} as $ingredientJson) {
                $exist = false;
                foreach($recipe->getRecipeIngredient() as $currentRecipeIngredient){
                    if (isset($ingredientJson->{'name'}) && isset($ingredientJson->{'count'})){
                        if ($ingredientJson->{'name'} == $currentRecipeIngredient->getIngredient()->getName()){
                            $exist = true;
                        }
                    }
                }
                if (!$exist){
                    $ingredient = $em->getRepository('RecipesBundle:Ingredient')->findOneBy(array('name' => $ingredientJson->{'name'}));
                    if (!$ingredient) {
                        $ingredient = new Ingredient();
                        $ingredient->setName($ingredientJson->{'name'});
                        $em->persist($ingredient);
                        $em->flush();
                    }
                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setCount($ingredientJson->{'count'});

                    $measureUnit = $em->getRepository('RecipesBundle:MeasureUnit')->find($ingredientJson->{'measureUnit'});
                    $recipeIngredient->setMeasureUnit($measureUnit);

                    $em->persist($recipeIngredient);
                }
            }

            // удаляем ненужные
            foreach($recipeIngredients as $currentRecipeIngredient){
                $exist = false;
                foreach ($json->{'ingredients'} as $ingredientJson) {
                    if (isset($ingredientJson->{'name'}) && isset($ingredientJson->{'count'})){
                        if ($ingredientJson->{'name'} == $currentRecipeIngredient->getIngredient()->getName()){
                            $exist = true;
                        }
                    }
                }
                if (!$exist){
                    $em->remove($currentRecipeIngredient);
                    $em->persist($recipe);
                    $em->flush();
                }
            }

            $image = $request->files->get('image');
            if($image){
                $recipe->setFile($image);
                $recipe->upload();
            }

            $em->flush();
            $em->getConnection()->commit();

        } catch (\Exception $e) {
            $em->getConnection()->rollback(); // откатываем транзакцию
            $em->close();
            throw $e;
        }

        $response = new Response(json_encode(array('id' => $recipe->getId())));
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/recipes/")
     * @Method("Post")
     * @Rest\View
     */
    public function newRecipeAction()
    {
        return $this->processJSON();
    }

    private function processJSON()
    {
        $request = $this->getRequest();
        $json = json_decode($request->request->get('model'));

        $em = $this->getDoctrine()->getManager();

        $creatorUid = $json->{'creator'};

        if (!$creatorUid){
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        $em->getConnection()->beginTransaction();
        try {
            $creator = $em->getRepository('RecipesBundle:User')->findOneBy(array('id' => $creatorUid));
            if (!$creator){
                $creator = new User();
                $creator->setId($creatorUid);
                $em->persist($creator);

                $metadata = $em->getClassMetaData(get_class($creator));
                $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

                $em->flush();
            }

            $recipe = new Recipe();
            $recipe->setName($json->{'name'});
            $recipe->setDescription($json->{'description'});
            $recipe->setCreator($creator);

            $image = $request->files->get('image');
            $recipe->setFile($image);
            $recipe->upload();

            $cuisine = null;
            if (isset($json->{'cuisine'})){
                $cuisine = $em->getRepository('RecipesBundle:Cuisine')->findOneBy(array('name' => $json->{'cuisine'}));
            }
            $category = $em->getRepository('RecipesBundle:Category')->findOneBy(array('name' => $json->{'category'}));

            if (!$cuisine) {
                $cuisine = new Cuisine();
                $cuisine->setName($json->{'cuisine'});
                $em->persist($cuisine);
                $em->flush();
            }
            if (!$category) {
                $category = new Category();
                $category->setName($json->{'category'});
                $em->persist($category);
                $em->flush();
            }

            $recipe->setCategory($category);

            if (isset($cuisine)){
                $recipe->setCuisine($cuisine);
            }

            $em->persist($recipe);
            $em->flush();

            foreach ($json->{'ingredients'} as &$ingredientJson) {
                if (isset($ingredientJson->{'name'}) && isset($ingredientJson->{'count'})){
                    $ingredient = $em->getRepository('RecipesBundle:Ingredient')->findOneBy(array('name' => $ingredientJson->{'name'}));
                    if (!$ingredient) {
                        $ingredient = new Ingredient();
                        $ingredient->setName($ingredientJson->{'name'});
                        $em->persist($ingredient);
                        $em->flush();
                    }
                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setCount($ingredientJson->{'count'});

                    $measureUnit = $em->getRepository('RecipesBundle:MeasureUnit')->find($ingredientJson->{'measureUnit'}->{'id'});
                    $recipeIngredient->setMeasureUnit($measureUnit);

                    $em->persist($recipeIngredient);
                }
            }
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback(); // откатываем транзакцию
            $em->close();
            throw $e;
        }

        $response = new Response(json_encode(array('id' => $recipe->getId())));
        $response->setStatusCode(201);

        return $response;
    }
}
