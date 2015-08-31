<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 19/02/2015
 * Time: 13:54
 */

namespace RM\PlantillaBundle\Controller;


use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Form\PlantillaType;
use Symfony\Component\HttpFoundation\Request;

class PlantillaController extends RMController
{


    /**
     * Busca la plantilla asignada a la comunicacion
     *
     * @param $idComunicacion
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($idComunicacion)
    {

        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        if(!$comunicacion instanceof Comunicacion) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
            $this->redirect($this->generateUrl('rm_plantilla_plantilla_index', ['idComunicacion' => $idComunicacion]));
        }

        $plantilla = $comunicacion->getPlantilla();

        if(!$plantilla instanceof Plantilla) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
            $this->redirect($this->generateUrl('rm_plantilla_plantilla_index', ['idComunicacion' => $idComunicacion]));
        }

        $editForm   = $this->createEditForm($plantilla, $comunicacion);

        return $this->render('RMPlantillaBundle:Plantilla:edit.html.twig', [
                'plantilla'    => $plantilla,
                'edit_form'    => $editForm->createView(),
                'comunicacion' => $comunicacion
            ]);
    }

    /**
     * Crea una nueva Plantilla
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $idComunicacion)
    {
        $em = $this->getManager();

        $plantilla = new Plantilla();

        $form = $this->createCreateForm($plantilla);

        $form->handleRequest($request);

        if($form->isValid()) {

            $em->persist($plantilla);
            $em->flush();

            return $this->redirect($this->generateUrl('rm_plantilla_plantilla_edit', ['id' => $plantilla->getIdPlantilla()]));
        }

        return $this->render('RMPlantillaBundle:Plantilla:new.html.twig', [
                'plantilla'
            ]);
    }

    /**
     * Genera un formulario del tipo Plantilla
     *
     * @param Plantilla $plantilla
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Plantilla $plantilla)
    {
        $em = $this->getManager();

        $form = $this->createForm(new PlantillaType(), $plantilla, [
                'action' => $this->generateUrl('rm_plantilla_plantilla_create'),
                'method' => 'POST',
                'em'     => $this->getManager()
         ]);

        $form->add('submit', 'submit', ['label' => 'Guardar']);

        return $form;
    }

    /**
     * Renderiza el formulario de creacion de una PlantillaModelo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction($idComunicacion)
    {
        $plantilla  = new Plantilla();
        $form       = $this->createCreateForm($plantilla);

        return $this->render('RMPlantillaBundle:Plantilla:new.html.twig', [
                'entity'    => $plantilla,
                'form'      => $form->createView(),
            ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, $idComunicacion)
    {
        $em = $this->getManager();

        $plantilla      = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);
        $comunicacion   = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);


        if(!$plantilla instanceof Plantilla) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
            $this->redirect($this->generateUrl('rm_plantilla_plantilla_index'));
        }

        $editForm   = $this->createEditForm($plantilla, $comunicacion);

        return $this->render('RMPlantillaBundle:Plantilla:edit.html.twig', [
                'plantilla'    => $plantilla,
                'edit_form'    => $editForm->createView(),
                'comunicacion' => $comunicacion
            ]);
    }

    /**
     * @param Plantilla $plantilla
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Plantilla $plantilla, Comunicacion $comunicacion)
    {
        $form = $this->createForm(new PlantillaType(), $plantilla, [
            'action'                 => $this->generateUrl('rm_plantilla_plantilla_update', [
                    'id'             => $plantilla->getIdPlantilla(),
                    'idComunicacion' => $comunicacion->getIdComunicacion()
                ]),
            'method' => 'PUT'
        ]);

        $form->add('submit', 'submit', ['label' => 'Actualizar']);

        return $form;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id, $idComunicacion)
    {
        $em = $this->getManager();

        $plantilla      = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);
        $comunicacion   = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        if(!$plantilla) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
        }

        $editForm = $this->createEditForm($plantilla, $comunicacion);
        $editForm->handleRequest($request);

        if($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.ok.actualizar');
            return $this->redirect($this->generateUrl('rm_plantilla_plantilla_edit', [
                        'id'             => $plantilla->getIdPlantilla(),
                        'idComunicacion' => $idComunicacion]
                ));
        }

        return $this->render('RMPlantillaBundle:Plantilla:edit.html.twig', [
                'plantilla'    => $plantilla,
                'edit_form'    => $editForm,
                'comunicacion' => $comunicacion
            ]);
    }


    /*

    public function deleteAction(Request $request, $id, $idComunicacion)
    {
        $form = $this->createDeleteForm($id, $idComunicacion);
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getManager();

            $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($id);

            if(!$plantilla) {
                $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
                $this->redirect($this->generateUrl('rm_plantilla_plantilla_index'));
            }

            $plantilla->setEstado(-1);
            $em->flush();

            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.ok.eliminar');
        }

        return $this->redirect($this->generateUrl('rm_plantilla_plantilla_index'));
    }


    private function createDeleteForm($id, $idComunicacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rm_plantilla_plantilla_delete', array(
                        'id' => $id,
                        'idComunicacion' => $idComunicacion))
            )
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm();
    }


    public function listAction(Request $request)
    {

    }*/
}
