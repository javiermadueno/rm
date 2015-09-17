<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 19/02/2015
 * Time: 17:40
 */

namespace RM\PlantillaBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Form\GrupoSlotsType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GrupoSlotsController
 *
 * @package RM\PlantillaBundle\Controller
 */
class GrupoSlotsController extends RMController
{

    /**
     * Muestra los grupos de slots de una plantilla determinada
     *
     * @param $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        $plantilla   = $comunicacion->getPlantilla();
        $idPlantilla = $plantilla->getIdPlantilla();

        $gruposSlots = $em->getRepository('RMPlantillaBundle:GrupoSlots')->findGruposSlotsByPlantilla($idPlantilla);

        return $this->render('RMPlantillaBundle:GrupoSlots:index.html.twig', [
            'entities'       => $gruposSlots,
            'plantilla'      => $plantilla,
            'idComunicacion' => $idComunicacion,
            'comunicacion'   => $comunicacion

        ]);
    }


    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);
        $plantilla    = $comunicacion->getPlantilla();

        if (!$plantilla) {
            throw $this->createNotFoundException('No se ha encontrado la plantilla');
        }

        $entity = new GrupoSlots();

        $formHandler = $this->get('rm.create_grupo_slot_form_handler');
        $form        = $this->createCreateForm($entity, $idComunicacion);

        if ($formHandler->handle($form, $request)) {
            $this->addFlash('mensaje', 'mensaje.ok.editar');

            return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
                'idComunicacion' => $comunicacion->getIdComunicacion()
            ]);
        }

        return $this->render('RMPlantillaBundle:GrupoSlots:new.html.twig', [
            'entity'       => $entity,
            'form'         => $form->createView(),
            'plantilla'    => $plantilla,
            'comunicacion' => $comunicacion
        ]);
    }


    /**
     * @param GrupoSlots $entity
     * @param            $idComunicacion
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCreateForm(GrupoSlots $entity, $idComunicacion)
    {
        $em           = $this->getManager();
        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);
        $plantilla    = $comunicacion->getPlantilla();

        if (!$plantilla) {
            $this->addFlash('error', 'mensaje.error.no.plantilla');

            return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
                'idComunicacion' => $comunicacion->getIdComunicacion()
            ]);
        }

        $entity->setIdPlantilla($plantilla);

        $formHandler = $this->get('rm.create_grupo_slot_form_handler');
        $form        = $formHandler->createForm($entity, [
            'action' => $this->generateUrl('rm_plantilla_gruposlots_create', [
                'idComunicacion' => $idComunicacion
            ]),
            'method' => 'POST',
            'em'     => $em
        ]);

        return $form;
    }


    /**
     * @param $id
     * @param $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id, $idComunicacion)
    {
        $em = $this->getManager();

        $entity = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoSlots entity.');
        }

        $comunicacion = $em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findById($idComunicacion);

        if(!$comunicacion instanceof Comunicacion) {
            throw $this->createNotFoundException('Unable to find Comunicacion');
        }


        return $this->render('RMPlantillaBundle:GrupoSlots:show.html.twig', [
            'entity'      => $entity,
            'comunicacion'=> $comunicacion
        ]);
    }


    /**
     * @param $id
     * @param $idComunicacion
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id, $idComunicacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rm_plantilla_gruposlots_delete',
                    ['id' => $id, 'idComunicacion' => $idComunicacion]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', ['label' => 'Eliminar'])
            ->getForm();
    }


    /**
     * @param $id
     * @param $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, $idComunicacion)
    {
        $em = $this->getManager();

        $entity       = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);
        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        $plantilla = $comunicacion->getPlantilla();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoSlots entity.');
        }

        if (!$plantilla) {
            throw $this->createNotFoundException('No se ha encontrado plantilla');
        }

        $editForm   = $this->createEditForm($entity, $idComunicacion);
        $deleteForm = $this->createDeleteForm($id, $idComunicacion);

        return $this->render('RMPlantillaBundle:GrupoSlots:edit.html.twig', [
            'entity'       => $entity,
            'edit_form'    => $editForm->createView(),
            'delete_form'  => $deleteForm->createView(),
            'comunicacion' => $comunicacion
        ]);
    }


    /**
     * @param GrupoSlots $entity
     * @param            $idComunicacion
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(GrupoSlots $entity, $idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        $form = $this->createForm(new GrupoSlotsType(), $entity, [
            'action' => $this->generateUrl('rm_plantilla_gruposlots_update', [
                'id'             => $entity->getIdGrupo(),
                'idComunicacion' => $comunicacion->getIdComunicacion()
            ]),
            'method' => 'PUT',
            'em'     => $em,
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }


    /**
     * @param Request $request
     * @param         $id
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id, $idComunicacion)
    {
        $em = $this->getManager();

        $entity       = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);
        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoSlots entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $idComunicacion);
        $editForm   = $this->createEditForm($entity, $idComunicacion);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->checkCreatividad();
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
                    'idComunicacion' => $idComunicacion
                ]
            );
        }

        return $this->render('RMPlantillaBundle:GrupoSlots:edit.html.twig', [
            'entity'       => $entity,
            'edit_form'    => $editForm->createView(),
            'delete_form'  => $deleteForm->createView(),
            'comunicacion' => $comunicacion
        ]);
    }


    /**
     * @param Request $request
     * @param         $id
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id, $idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);
        $form         = $this->createDeleteForm($id, $idComunicacion);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GrupoSlots entity.');
            }

            $this->get('rm.grupo_slot_manager')->delete($entity);
        }

        return $this->redirect($this->generateUrl('rm_comunicacion.comunicacion.editar_plantilla', [
            'idComunicacion' => $comunicacion->getIdComunicacion()
        ]));
    }

    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function borrarAction(Request $request, $idComunicacion)
    {
        $em           = $this->getManager();
        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);


        $idsGruposAEliminar = $request->request->get('eliminar', []);

        if(! $this->get('rm.grupo_slot_manager')->deleteByIds($idsGruposAEliminar)){
            $this->addFlash('mensaje', 'mensaje.error.eliminar');

            return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
                'idComunicacion' => $comunicacion->getIdComunicacion()
            ]);
        }

        $this->addFlash('mensaje', 'mensaje.ok.eliminar');

        return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
            'idComunicacion' => $comunicacion->getIdComunicacion()
        ]);

    }

} 