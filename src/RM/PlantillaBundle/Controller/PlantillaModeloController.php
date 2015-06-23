<?php

namespace RM\PlantillaBundle\Controller;

use Doctrine\ORM\EntityManager;
use RM\AppBundle\Controller\RMController;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Event\PlantillaEvent;
use RM\PlantillaBundle\Event\PlantillaEvents;
use RM\PlantillaBundle\Form\PlantillaModeloType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlantillaModeloController extends RMController
{

    /**
     * Muestra un listado de todas las plantillas
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getManager();

        $plantillas = $em->getRepository('RMPlantillaBundle:Plantilla')->findAllPlantillasModelo();

        $canales = $em->getRepository('RMComunicacionBundle:Canal')->findAll();

        return $this->render('RMPlantillaBundle:PlantillaModelo:index.html.twig', [
            'plantillas' => $plantillas,
            'objCanales' => $canales,
        ]);
    }



    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateIndexAction(Request $request)
    {
        $em = $this->getManager();

        $idCanal = $request->request->get('id_canal', -1);

        $plantillas = $em->getRepository('RMPlantillaBundle:Plantilla')->findPlantillasModeloByCanal($idCanal);

        return $this->render('RMPlantillaBundle:PlantillaModelo:listaPlantillas.html.twig', [
            'plantillas' => $plantillas
        ]);
    }

    /**
     * Crea una nueva PlantillaModelo
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $plantilla = new Plantilla();
        $form      = $this->createCreateForm($plantilla);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($plantilla);
            $em->flush();

            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', ['id' => $plantilla->getIdPlantilla()]);
        }

        return $this->render('RMPlantillaBundle:PlantillaModelo:new.html.twig', [
            'plantilla'
        ]);
    }

    /**
     * Genera un formulario del tipo PlantillaModelo
     *
     * @param Plantilla $plantilla
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Plantilla $plantilla)
    {
        $form = $this->createForm(new PlantillaModeloType(), $plantilla, [
            'action' => $this->generateUrl('rm_plantilla_plantilla_modelo_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Guardar']);

        return $form;
    }

    /**
     * Renderiza el formulario de creacion de una PlantillaModelo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $plantilla = new Plantilla();
        $form      = $this->createCreateForm($plantilla);

        return $this->render('RMPlantillaBundle:PlantillaModelo:new.html.twig', [
            'entity' => $plantilla,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $em = $this->getManager();

        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);


        if (!$plantilla instanceof Plantilla) {
            $this->addFlash('error', 'mensaje.error.no.plantillamodelo');
            $this->redirectToRoute('rm_plantilla_plantilla_modelo_index');
        }

        $comunicaciones_generadas = $em->getRepository('RMComunicacionBundle:Comunicacion')
            ->findByPlantilla($plantilla->getIdPlantilla());

        $editable = empty($comunicaciones_generadas);

        $editForm   = $this->createEditForm($plantilla);
        $deleteForm = $this->createDeleteForm($plantilla->getIdPlantilla());

        return $this->render('RMPlantillaBundle:PlantillaModelo:edit.html.twig', [
            'plantilla'   => $plantilla,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'editable'    => $editable
        ]);
    }

    /**
     * @param Plantilla $plantilla
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Plantilla $plantilla)
    {
        $form = $this->createForm(new PlantillaModeloType(), $plantilla, [
            'action' => $this->generateUrl('rm_plantilla_plantilla_modelo_update',
                ['id' => $plantilla->getIdPlantilla()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Actualizar']);

        return $form;
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rm_plantilla_plantilla_modelo_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Eliminar'])
            ->getForm();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getManager();

        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);

        if (!$plantilla) {
            $this->addFlash('error', 'mensaje.error.no.plantillamodelo');
        }

        $editForm = $this->createEditForm($plantilla);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->addFlash('mensaje', 'mensaje.ok.actualizar');

            return $this->redirectToRoute('rm_plantilla_plantilla_modelo_edit', [
                    'id' => $plantilla->getIdPlantilla()
                ]
            );
        }

        return $this->render('RMPlantillaBundle:PlantillaModelo:edit.html.twig', [
            'plantilla' => $plantilla,
            'edit_form' => $editForm,
        ]);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getManager();

            $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);

            if (!$plantilla || !$plantilla->getEditable()) {
                $this->addFlash('error', 'mensaje.error.eliminar');
                $this->redirectToRoute('rm_plantilla_plantilla_modelo_index');
            }

            $plantilla->setEstado(-1);
            $em->flush();

            $this->addFlash('mensaje', 'mensaje.ok.eliminar');
        }

        return $this->redirectToRoute('rm_plantilla_plantilla_modelo_index');
    }

    /**
     * @param Request $request
     * @param         $ids
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePlantillasAction(Request $request, $ids)
    {
        $ids = !is_string($ids) ?: explode(',', $ids);

        foreach ($ids as $id) {
            $this->delete($id);
        }
        $em = $this->getManager();

        $idCanal = $request->get('id_canal', -1);

        $plantillas = $em->getRepository('RMPlantillaBundle:Plantilla')->findPlantillasModeloByCanal($idCanal);

        return $this->render('RMPlantillaBundle:PlantillaModelo:listaPlantillas.html.twig', [
            'plantillas' => $plantillas
        ]);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    private function delete($id)
    {
        $em = $this->getManager();

        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);

        if (!$plantilla instanceof Plantilla) {
            $this->addFlash('error', 'mensaje.error.no.plantillamodelo');
            return false;
        }

        if(!$plantilla->getEditable()) {
            $this->addFlash('mensaje', 'mensaje.error.eliminar');
            return false;
        }

        $plantilla->setEstado(-1);
        $em->flush();

        $this->get('event_dispatcher')
            ->dispatch(PlantillaEvents::ELIMINAR_PLANTILLA, new PlantillaEvent($plantilla));

        return true;
    }
}
