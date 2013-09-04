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
use Recipes\RecipesBundle\Entity\Comment;

class CommentController extends Controller
{
    /**
     * @Route("/comments")
     * @Method("GET")
     * @Rest\View
     */
    public function getAllCommentsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RecipesBundle:Comment')->findAll();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($categories)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/comments")
     * @Method("POST")
     * @Rest\View
     */
    public function newCommentAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/comments/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getCommentAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('RecipesBundle:Comment')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
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
     * @Route("/comments/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function editCommentAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/comments/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteCommentAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('RecipesBundle:Comment')->find($id);
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
            $category = $em->getRepository('RecipesBundle:Comment')->find($json->{'id'});
            if (!$category) {
                // todo: сущности не существует
            }
        } else {
            $category = new Comment();
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
