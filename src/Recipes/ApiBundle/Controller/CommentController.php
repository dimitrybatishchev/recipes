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
        $comments = $em->getRepository('RecipesBundle:Comment')->findAll();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($comments)
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
        return $this->processJSON();
    }

    /**
     * @Route("/comments/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function editCommentAction()
    {
        return $this->processJSON();
    }

    /**
     * @Route("/comments/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getCommentAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $comment = $em->getRepository('RecipesBundle:Comment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        } else {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($comment)
                ->setFormat('json');
        }

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/comments/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteCommentAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('RecipesBundle:Comment')->find($id);
        if (!$comment) {
            $response = new Response();
            $response->setStatusCode(404);
            return $response;
        }
        $em->remove($comment);
        $em->flush();
        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    private function processJSON()
    {
        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        if (array_key_exists('id', $json)){
            $comment = $em->getRepository('RecipesBundle:Comment')->find($json->{'id'});
            if (!$comment) {
                $response = new Response();
                $response->setStatusCode(404);
                return $response;
            }
        } else {
            $comment = new Comment();
        }
        $comment->setName($json->{'name'});
        $em->persist($comment);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }
}
