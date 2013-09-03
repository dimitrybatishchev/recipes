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

class UserController extends Controller
{
    /**
     * @Route("/users")
     * @Method("GET")
     * @Rest\View
     */
    public function getAllUsers()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('RecipesBundle:User')->findAll();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($categories)
            ->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Route("/users/login")
     * @Method("POST")
     * @Rest\View
     */
    public function loginUserAction()
    {
        $request = $this->getRequest();
        $json = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('RecipesBundle:User')->find($json->{'id'});

        if(!$user){
            $user = new User();
            $user->setId($json->{'id'});
            $user->setFirstname($json->{'firstname'});
            $user->setLastname($json->{'lastname'});
            $user->setAvatar($json->{'avatar'});
            $em->persist($user);

            $metadata = $em->getClassMetaData(get_class($user));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }

        if($user){
            $user->setFirstname($json->{'firstname'});
            $user->setLastname($json->{'lastname'});
            $user->setAvatar($json->{'avatar'});
            $em->persist($user);
        }

        $em->flush();

        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/users")
     * @Method("POST")
     * @Rest\View
     */
    public function newUserAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/users/{id}")
     * @Method("GET")
     * @Template()
     */
    public function getUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('RecipesBundle:User')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find User entity.');
        } else {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($category)
                ->setFormat('json');
            return $this->get('fos_rest.view_handler')->handle($view);
        }
    }

    /**
     * @Route("/users/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function editUserAction()
    {
        return $this->processForm();
    }

    /**
     * @Route("/users/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteUserAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('RecipesBundle:User')->find($id);
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
            $category = $em->getRepository('RecipesBundle:User')->find($json->{'id'});
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
