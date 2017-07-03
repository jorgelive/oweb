<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Caracteristicatipo;
use Gopro\InventarioBundle\Form\CaracteristicatipoType;

/**
 * Caracteristicatipo controller.
 *
 * @Route("/caracteristicatipo")
 */
class CaracteristicatipoController extends BaseController
{

    /**
     * Lists all Caracteristicatipo entities.
     *
     * @Route("/", name="gopro_inventario_caracteristicatipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproInventarioBundle:Caracteristicatipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Caracteristicatipo entity.
     *
     * @Route("/create", name="gopro_inventario_caracteristicatipo_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Caracteristicatipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Caracteristicatipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_caracteristicatipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Caracteristicatipo entity.
    *
    * @param Caracteristicatipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Caracteristicatipo $entity)
    {
        $form = $this->createForm(new CaracteristicatipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_caracteristicatipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Caracteristicatipo entity.
     *
     * @Route("/new", name="gopro_inventario_caracteristicatipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Caracteristicatipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Caracteristicatipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristicatipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristicatipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristicatipo.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Caracteristicatipo entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_caracteristicatipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristicatipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristicatipo.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Caracteristicatipo entity.
    *
    * @param Caracteristicatipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Caracteristicatipo $entity)
    {
        $form = $this->createForm(new CaracteristicatipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_caracteristicatipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Caracteristicatipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristicatipo_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Caracteristica:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristicatipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristicatipo.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_caracteristicatipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Caracteristicatipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristicatipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Caracteristicatipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristicatipo.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_caracteristicatipo'));
    }

    /**
     * Creates a form to delete a Caracteristicatipo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'deleteForm',
            'form',
            null,
            [
                'action'=>$this->generateUrl('gopro_inventario_caracteristicatipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}