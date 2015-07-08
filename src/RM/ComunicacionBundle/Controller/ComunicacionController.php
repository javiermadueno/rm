<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/03/2015
 * Time: 12:42
 */

namespace RM\ComunicacionBundle\Controller;


use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Event\ComunicacionEvent;
use RM\ComunicacionBundle\Event\ComunicacionEvents;
use RM\ComunicacionBundle\Form\Gestion\nuevaComunicacionType;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ComunicacionController extends Controller
{
    public function indexAction()
    {
        $em = $this->getManager();

        $comunicaciones = $em->getRepository('RMComunicacionBundle:Comunicacion')->findAll();
        $canales = $em->getRepository('RMComunicacionBundle:Canal')->findAll();

        return $this->render('RMComunicacionBundle:Comunicacion:index.html.twig', [
            'comunicaciones' => $comunicaciones,
            'canales'        => $canales
        ]);
    }

    private function getManager()
    {
        return $this->get('rm.manager')->getManager();
    }

    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            return $this->createNotFoundException(sprintf(
                "No se ha encontrado la comunicación con la id ='%s'", $idComunicacion
            ));
        }

        $segmentos = $em
            ->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->findSegmentosComunicacionByComunicacion($comunicacion);

        $servicioSegCom = $this->get("SegmentoComunicacionService");
        $objSegmentos = $servicioSegCom->getSegmentosComunicacionById($idComunicacion);

        $peticion = $request;

        $gruposSlot = $em->getRepository('RMPlantillaBundle:GrupoSlots')
            ->findGruposSlotsByComunicacion($idComunicacion);

        $formulario = $this->createForm(new nuevaComunicacionType ($comunicacion), $comunicacion);
        $formulario->handleRequest($peticion);

        if ($formulario->isValid()) {

            /**
             * Se comprueba que cumpla los requisitos para ponerla activa
             * 1. Tiene que tener segmentos asociados
             * 2. Tiene que tener plantilla asociada con gruposdeSlots
             */
            if (Comunicacion::ESTADO_ACTIVO == $comunicacion->getEstado()) {

                if ($comunicacion->getSegmentos()->isEmpty()) {
                    $this->get('session')->getFlashBag()->add('formulario', "mensaje.error.faltan.segmentos");
                    $comunicacion->setEstado(Comunicacion::ESTADO_PAUSADO);
                }

                if (!$gruposSlot) {
                    $this->get('session')->getFlashBag()->add('formulario', "mensaje.error.faltan.gruposslots");
                    $comunicacion->setEstado(Comunicacion::ESTADO_PAUSADO);
                }

                if ($objSegmentos && $gruposSlot) {
                    $this->get('session')->getFlashBag()->add('formulario_ok', "mensaje.ok.guardar");
                }

                $em->persist($comunicacion);
                $em->flush();

                return $this->redirectToRoute('direct_manager_edit_datos', ['idComunicacion' => $idComunicacion]);
            } else {
                $em->persist($comunicacion);
                $em->flush();
                $this->get('session')->getFlashBag()->add('formulario_ok', "mensaje.ok.guardar");
            }
        }

        return $this->render('RMComunicacionBundle:Edicion:editarComunicacion.html.twig', [
            'id_comunicacion' => $idComunicacion,
            'formulario'      => $formulario->createView(),
            'objSegmentos'    => $objSegmentos,
            'objComunicacion' => $comunicacion,
            'gruposSlots'     => $gruposSlot
        ]);
    }

    public function editPlantillaComunicacionAction($idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->findById($idComunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
            $this->redirectToRoute('rm_plantilla_plantilla_index', ['idComunicacion' => $idComunicacion]);
        }

        $plantilla = $comunicacion->getPlantilla();

        if (!$plantilla instanceof Plantilla) {
            $this->get('event_dispatcher')
                ->dispatch(ComunicacionEvents::NUEVA_COMUNICACION, new ComunicacionEvent($comunicacion));

            $plantilla = $comunicacion->getPlantilla();
        }

        return $this->render('RMPlantillaBundle:Plantilla:edit.html.twig', [
            'plantilla'    => $plantilla,
            'comunicacion' => $comunicacion
        ]);
    }


} 