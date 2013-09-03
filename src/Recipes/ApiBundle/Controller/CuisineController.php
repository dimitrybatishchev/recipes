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

class CuisineController extends Controller
{
    /**
     * @Route("/cuisines/")
     * @Method("GET")
     * @Rest\View
     */
    public function getAllCuisinesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cuisines = $em->getRepository('RecipesBundle:Cuisine')->findAll();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($cuisines)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/cuisines/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getCuisineAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $cuisine = $em->getRepository('RecipesBundle:Cuisine')->find($id);

        if (!$cuisine) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        } else {
            // todo: как эта штука работает?
            $view = View::create()
                ->setStatusCode(200)
                ->setData($cuisine)
                ->setFormat('json');
            return $this->get('fos_rest.view_handler')->handle($view);
        }
    }

    /**
     * @Route("/cuisines/search/{name}")
     * @Method("GET")
     * @Template()
     */
    public function getCuisineByNameAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $q = $em->createQuery("select c from RecipesBundle:Cuisine c where c.name like :name")->setParameter('name', '%'.$name.'%');
        $cuisines = $q->getResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($cuisines)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/cuisines/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function editCuisineAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/cuisines/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteCuisineAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $cuisine = $em->getRepository('RecipesBundle:Cuisine')->find($id);
        if (!$cuisine) {
            // todo: сущности не существует
        }
        $em->remove($cuisine);
        $em->flush();
        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/cuisines")
     * @Method("POST")
     * @Rest\View
     */
    public function newCuisineAction()
    {
        return $this->processForm();
    }


    private function processForm()
    {
        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        if (array_key_exists('id', $json)){
            $cuisine = $em->getRepository('RecipesBundle:Cuisine')->find($json->{'id'});
            if (!$cuisine) {
                // todo: сущности не существует
            }
        } else {
            $cuisine = new Cuisine();
        }
        $cuisine->setName($json->{'name'});
        $em->persist($cuisine);
        $em->flush();
        // todo: что за херня
        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }
}
