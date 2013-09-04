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

class IngredientController extends Controller
{
    /**
     * @Route("/ingredients/search/{name}")
     * @Method("GET")
     * @Template()
     */
    public function getIngredientByNameAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery("select c from RecipesBundle:Ingredient c where c.name like :name")->setParameter('name', '%'.$name.'%');
        $ingredients = $q->getResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($ingredients)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

}
