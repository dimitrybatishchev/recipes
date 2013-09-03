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
    public function getAllRecipesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder('Recipes')
                ->select('r')
                ->from('RecipesBundle:Recipe', 'r')
                ->orderBy('r.id', 'ASC')
                ->setMaxResults(5)
                ->setFirstResult(0);
        $query = $qb->getQuery();
        $recipes = $query->getResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($recipes)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/recipes/last/{count}")
     * @Method("GET")
     * @Rest\View
     */
    public function getLastRecipesAction($count)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder('Recipes')
            ->select('r')
            ->from('RecipesBundle:Recipe', 'r')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults($count);
        $query = $qb->getQuery();
        $recipes = $query->getResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($recipes)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/recipes/page/{index}")
     * @Method("GET")
     * @Rest\View
     */
    public function getRecipesPageAction($index)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder('Recipes')
            ->select('r')
            ->from('RecipesBundle:Recipe', 'r')
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(5)
            ->setFirstResult($index*5);
        $query = $qb->getQuery();
        $recipes = $query->getResult();

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
     * @Route("/recipes/{recipeId}/addToFavorite")
     * @Method("POST")
     * @Template()
     */
    public function addToFavoriteAction($recipeId)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('RecipesBundle:Recipe')->find($recipeId);

        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $userId = $json->{'userId'};

        $user = $em->getRepository('RecipesBundle:User')->find($userId);
        $user->addLikedRecipe($recipe);

        $em->persist($recipe);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/recipes/{recipeId}/addComment")
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
     * @Route("/recipes/search/")
     * @Method("POST")
     * @Rest\View
     */
    public function searchRecipeAction()
    {
        $request = $this->getRequest();

        $json = json_decode($request->getContent());

        if (isset($json->{'selectedCuisine'})){
            $selectedCuisineId = $json->{'selectedCuisine'};
        }
        if (isset($json->{'selectedCategory'})){
            $selectedCategoryId = $json->{'selectedCategory'};
        }
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder('Recipes')
            ->select('r')
            ->from('RecipesBundle:Recipe', 'r')
            ->leftJoin('r.cuisine', 'cuisine')
            ->leftJoin('r.category', 'category');

        if (isset($selectedCuisineId)){
            $qb = $qb->where('cuisine.id = :cuisineId AND category.id = :categoryId');
        } else {
            $qb = $qb->where('category.id = :categoryId');
        }
        if (isset($selectedCuisineId)){
            $qb = $qb->setParameter('cuisineId', $selectedCuisineId);
        }
        $qb = $qb->setParameter('categoryId', $selectedCategoryId);

        $query = $qb->getQuery();
        $recipes = $query->getResult();


        $matchedRecipes = array();


        foreach ($recipes as $recipe){
            $matched = 0;
            $requiredIngredientsCount = count($json->{'selectedIngredients'});
            foreach($recipe->getRecipeIngredient() as $recipeIngredient){
                $ingredientName = $recipeIngredient->getIngredient()->getName();
                foreach($json->{'selectedIngredients'} as $requiredIngredient){
                    if ($ingredientName === $requiredIngredient){
                        $matched++;
                    }
                }
            }
            if ($matched === $requiredIngredientsCount){
                array_push($matchedRecipes, $recipe);
            }
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($matchedRecipes)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);

        // todo: что за херня
        $response = new Response();
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
        return $this->processForm();
    }

    private function processForm()
    {
        $request = $this->getRequest();
        $json = json_decode($request->request->get('model'));

        $em = $this->getDoctrine()->getManager();

        $creatorUid = $json->{'creator'};

        print_r($creatorUid);

        if (!$creatorUid){
            $response = new Response();
            $response->setStatusCode(400);
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


            if (isset($json->{'cuisine'})){
                $cuisine = $em->getRepository('RecipesBundle:Cuisine')->findOneBy(array('name' => $json->{'cuisine'}));
            }
            $category = $em->getRepository('RecipesBundle:Category')->findOneBy(array('name' => $json->{'category'}));

            if (isset($cuisine)) {
                if (!$cuisine){
                    $cuisine = new Cuisine();
                    $cuisine->setName($json->{'cuisine'});
                    $em->persist($cuisine);
                    $em->flush();
                }
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
