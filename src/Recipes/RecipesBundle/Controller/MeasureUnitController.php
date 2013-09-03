<?php

namespace Recipes\RecipesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Recipes\RecipesBundle\Entity\MeasureUnit;
use Recipes\RecipesBundle\Form\MeasureUnitType;

/**
 * MeasureUnit controller.
 *
 * @Route("/mu")
 */
class MeasureUnitController extends Controller
{

    /**
     * Lists all MeasureUnit entities.
     *
     * @Route("/", name="mu")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RecipesBundle:MeasureUnit')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new MeasureUnit entity.
     *
     * @Route("/", name="mu_create")
     * @Method("POST")
     * @Template("RecipesBundle:MeasureUnit:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new MeasureUnit();
        $form = $this->createForm(new MeasureUnitType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mu_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new MeasureUnit entity.
     *
     * @Route("/new", name="mu_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MeasureUnit();
        $form   = $this->createForm(new MeasureUnitType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a MeasureUnit entity.
     *
     * @Route("/{id}", name="mu_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:MeasureUnit')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeasureUnit entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MeasureUnit entity.
     *
     * @Route("/{id}/edit", name="mu_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:MeasureUnit')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeasureUnit entity.');
        }

        $editForm = $this->createForm(new MeasureUnitType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MeasureUnit entity.
     *
     * @Route("/{id}", name="mu_update")
     * @Method("PUT")
     * @Template("RecipesBundle:MeasureUnit:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RecipesBundle:MeasureUnit')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MeasureUnit entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MeasureUnitType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mu_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a MeasureUnit entity.
     *
     * @Route("/{id}", name="mu_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RecipesBundle:MeasureUnit')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MeasureUnit entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mu'));
    }

    /**
     * Creates a form to delete a MeasureUnit entity by id.
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
