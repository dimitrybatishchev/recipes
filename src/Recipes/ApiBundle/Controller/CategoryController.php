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

class CategoryController extends Controller
{
    /**
     * @Route("/categories")
     * @Method("GET")
     * @Rest\View
     */
    public function getAllCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RecipesBundle:Category')->findAll();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($categories)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/categories")
     * @Method("POST")
     * @Rest\View
     */
    public function newCategoryAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/categories/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('RecipesBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        } else {
            // todo: как эта штука работает?
            $view = View::create()
                ->setStatusCode(200)
                ->setData($category)
                ->setFormat('json');
            return $this->get('fos_rest.view_handler')->handle($view);
        }
    }

    /**
     * @Route("/categories/search/{name}")
     * @Method("GET")
     * @Template()
     */
    public function getCategoryByNameAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery("select c from RecipesBundle:Category c where c.name like :name")->setParameter('name', '%'.$name.'%');
        $categories = $q->getResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($categories)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/categories/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function editCategoryAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/categories/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteCategoryAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('RecipesBundle:Category')->find($id);
        if (!$category) {
            // todo: сущности не существует
        }
        $em->remove($category);
        $em->flush();
        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    private function processForm()
    {
        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        if (array_key_exists('id', $json)){
            $category = $em->getRepository('RecipesBundle:Category')->find($json->{'id'});
            if (!$category) {
                // todo: сущности не существует
            }
        } else {
            $category = new Category();
        }
        $category->setName($json->{'name'});
        $em->persist($category);
        $em->flush();
        // todo: что за херня
        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }
}
