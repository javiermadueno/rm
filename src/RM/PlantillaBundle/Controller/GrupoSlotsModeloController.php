<?php

namespace RM\PlantillaBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Form\GrupoSlotsType;
use Symfony\Component\HttpFoundation\Request;


/**
 * GrupoSlotsModelo controller.
 *
 */
class GrupoSlotsModeloController extends RMController
{


    /**
     * Muestra todas los GruposSlot pertenecientes a una plantilla
     *
     * @param $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($idPlantilla)
    {
        $em = $this->getManager();

        $entities = $em->getRepository('RMPlantillaBundle:GrupoSlots')
            ->findGruposSlotsByPlantilla($idPlantilla);

        $editable = $this->get('request')->get('editable', true);

        if ($editable) {
            return $this->render('RMPlantillaBundle:GrupoSlotsModelo:index.html.twig', [
                'entities'  => $entities,
                'plantilla' => $idPlantilla,
                'editable'  => $editable

            ]);
        } else {
            return $this->render('RMPlantillaBundle:GrupoSlotsModelo:indexNoEditable.html.twig', [
                'entities'  => $entities,
                'plantilla' => $idPlantilla
            ]);
        }


    }


    /**
     * @param Request $request
     * @param         $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $idPlantilla)
    {

        $entity = new GrupoSlots();
        $form = $this->createCreateForm($entity, $idPlantilla);

        $formHandler = $this->get('rm.create_grupo_slot_form_handler');

        if ($formHandler->handle($form, $request)) {
            $this->addFlash('mensaje', 'mensaje.ok.crear');
            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $idPlantilla]);
        }

        return $this->render('RMPlantillaBundle:GrupoSlotsModelo:new.html.twig', [
            'entity'    => $entity,
            'form'      => $form->createView(),
            'plantilla' => $idPlantilla,
        ]);
    }


    /**
     * @param GrupoSlots $entity
     * @param            $idPlantilla
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCreateForm(GrupoSlots $entity, $idPlantilla)
    {
        $em = $this->getManager();
        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($idPlantilla);

        if (!$plantilla) {
            $this->addFlash('error', 'mensaje.error.no.plantillamodelo');
            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_index');
        }

        $entity->setIdPlantilla($plantilla);

        $form = $this->get('rm.create_grupo_slot_form_handler')->createForm($entity, [
            'action' => $this->generateUrl('gruposlotsmodelo_create', [
                'idPlantilla' => $idPlantilla
            ]),
            'method' => 'POST',
            'em'     => $em
        ]);

        return $form;
    }


    /**
     * @param $id
     * @param $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id, $idPlantilla)
    {
        $em = $this->getManager();

        $entity = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoSlotsModelo entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $idPlantilla);

        return $this->render('RMPlantillaBundle:GrupoSlotsModelo:show.html.twig', [
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'plantilla'   => $idPlantilla
        ]);
    }


    /**
     * Genera el formulario de borrado de un GrupoSlot
     *
     * @param $id
     * @param $idPlantilla
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id, $idPlantilla)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gruposlotsmodelo_delete', ['id' => $id, 'idPlantilla' => $idPlantilla]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Eliminar'])
            ->getForm();
    }


    /**
     * Actualiza un GrupoSlot
     *
     * @param Request $request
     * @param         $id
     * @param         $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $idPlantilla)
    {
        $em = $this->getManager();

        $entity = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoSlotsModelo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id, $idPlantilla);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'mensaje.ok.editar');
            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $idPlantilla]);
        }

        return $this->render('RMPlantillaBundle:GrupoSlotsModelo:edit.html.twig', [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'plantilla'   => $idPlantilla,
        ]);

    }


    /**
     * @param GrupoSlots $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(GrupoSlots $entity)
    {
        $em = $this->getManager();

        $form = $this->createForm(new GrupoSlotsType(), $entity, [
            'action' => $this->generateUrl('gruposlotsmodelo_update', [
                'id'          => $entity->getIdGrupo(),
                'idPlantilla' => $entity->getIdPlantilla()->getIdPlantilla()
            ]),
            'method' => 'PUT',
            'em'     => $em,
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }


    /**
     * Borra un solo GrupoSlot mediante el formulario.
     *
     * @param Request $request
     * @param         $id
     * @param         $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id, $idPlantilla)
    {
        $form = $this->createDeleteForm($id, $idPlantilla);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getManager();
            $entity = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GrupoSlotsModelo entity.');
            }

            $this->get('rm.grupo_slot_manager')->delete($entity);

            $this->addFlash('mensaje', 'mensaje.ok.eliminar');
        }

        return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $idPlantilla]);
    }

    /**
     * Borra los GrupoSlots seleccionados mediante un checkbox.
     *
     * @param Request $request
     * @param         $idPlantilla
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function borrarAction(Request $request, $idPlantilla)
    {
        $idsGruposAEliminar = $request->request->get('eliminar', []);

        if (!$this->get('rm.grupo_slot_manager')->deleteByIds($idsGruposAEliminar)) {
            $this->addFlash('mensaje', 'mensaje.error.eliminar');
            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $idPlantilla]);
        }

        $this->addFlash('mensaje', 'mensaje.ok.eliminar');

        return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $idPlantilla]);
    }
}
