<?php

namespace Recipes\RecipesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Recipes\RecipesBundle\Entity\RecipeIngredient;
use Recipes\RecipesBundle\Form\RecipeIngredientType;

/**
 * RecipeIngredient controller.
 *
 * @Route("/recipe-ingredient")
 */
class RecipeIngredientController extends Controller
{

    /**
     * Lists all RecipeIngredient entities.
     *
     * @Route("/", name="recipe-ingredient")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RecipesBundle:RecipeIngredient')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new RecipeIngredient entity.
     *
     * @Route("/", name="recipe-ingredient_create")
     * @Method("POST")
     * @Template("RecipesBundle:RecipeIngredient:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new RecipeIngredient();
        $form = $this->createForm(new RecipeIngredientType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('recipe-ingredient_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new RecipeIngredient entity.
     *
     * @Route("/new", name="recipe-ingredient_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RecipeIngredient();
        $form   = $this->createForm(new RecipeIngredientType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a RecipeIngredient entity.
     *
     * @Route("/{id}", name="recipe-ingredient_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:RecipeIngredient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RecipeIngredient entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing RecipeIngredient entity.
     *
     * @Route("/{id}/edit", name="recipe-ingredient_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:RecipeIngredient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RecipeIngredient entity.');
        }

        $editForm = $this->createForm(new RecipeIngredientType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing RecipeIngredient entity.
     *
     * @Route("/{id}", name="recipe-ingredient_update")
     * @Method("PUT")
     * @Template("RecipesBundle:RecipeIngredient:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:RecipeIngredient')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RecipeIngredient entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RecipeIngredientType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('recipe-ingredient_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a RecipeIngredient entity.
     *
     * @Route("/{id}", name="recipe-ingredient_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RecipesBundle:RecipeIngredient')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RecipeIngredient entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('recipe-ingredient'));
    }

    /**
     * Creates a form to delete a RecipeIngredient entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
