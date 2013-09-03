<?php

namespace Recipes\RecipesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Recipes\RecipesBundle\Entity\Cuisine;
use Recipes\RecipesBundle\Form\CuisineType;

/**
 * Cuisine controller.
 *
 * @Route("/cuisine")
 */
class CuisineController extends Controller
{

    /**
     * Lists all Cuisine entities.
     *
     * @Route("/", name="cuisine")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RecipesBundle:Cuisine')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Cuisine entity.
     *
     * @Route("/", name="cuisine_create")
     * @Method("POST")
     * @Template("RecipesBundle:Cuisine:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Cuisine();
        $form = $this->createForm(new CuisineType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuisine_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Cuisine entity.
     *
     * @Route("/new", name="cuisine_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Cuisine();
        $form   = $this->createForm(new CuisineType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Cuisine entity.
     *
     * @Route("/{id}", name="cuisine_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:Cuisine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuisine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cuisine entity.
     *
     * @Route("/{id}/edit", name="cuisine_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:Cuisine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuisine entity.');
        }

        $editForm = $this->createForm(new CuisineType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Cuisine entity.
     *
     * @Route("/{id}", name="cuisine_update")
     * @Method("PUT")
     * @Template("RecipesBundle:Cuisine:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:Cuisine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuisine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CuisineType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuisine_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Cuisine entity.
     *
     * @Route("/{id}", name="cuisine_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RecipesBundle:Cuisine')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cuisine entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cuisine'));
    }

    /**
     * Creates a form to delete a Cuisine entity by id.
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
