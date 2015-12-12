<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/03/2015
 * Time: 12:42
 */

namespace RM\ComunicacionBundle\Controller;


use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Event\ComunicacionEvent;
use RM\ComunicacionBundle\Event\ComunicacionEvents;
use RM\ComunicacionBundle\Form\Gestion\nuevaComunicacionType;
use RM\ComunicacionBundle\Form\Type\ComunicacionType;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ComunicacionController extends RMController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getManager();
        $servicioCom = $this->get('comunicacionservice');

        $canal  = $request->get('canal');
        $estado = $request->get('estado');

        $comunicaciones = $em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findByCanalYEstado($canal, $estado);

        $canales = $em
            ->getRepository('RMComunicacionBundle:Canal')
            ->findAll();

        /** @var Comunicacion $comunicacion */
        foreach ($comunicaciones as $comunicacion) {
            $comunicacion->proximaEjecucion();
        }

        return $this->render('RMComunicacionBundle:Comunicacion:index.html.twig', [
            'comunicaciones' => $comunicaciones,
            'canales'        => $canales,
            'id_canal'       => $canal,
            'estado'         => $estado
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getManager();

        $comunicacion = new Comunicacion();
        $comunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);

        $form = $this->createForm(new ComunicacionType($comunicacion), $comunicacion, [
            'em' => $em,
            'action' => $this->generateUrl('direct_manager_new'),
            'method' => Request::METHOD_POST
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);
            $comunicacion->setGenerada(false);

            $em->persist($comunicacion);
            $em->flush();

            $this
                ->get('event_dispatcher')
                ->dispatch(
                    ComunicacionEvents::NUEVA_COMUNICACION,
                    new ComunicacionEvent($comunicacion)
                );

            $id_comunicacion = $comunicacion->getIdComunicacion();

            return $this->redirectToRoute('direct_manager_edit_datos', ['idComunicacion' => $id_comunicacion]);

        }
        return $this->render('RMComunicacionBundle:Comunicacion:new.html.twig', [
            'formulario'      => $form->createView()
        ]);
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


        $formulario = $this->createForm(new ComunicacionType($comunicacion), $comunicacion, [
            'em' => $em,
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('direct_manager_edit_datos', ['idComunicacion' => $idComunicacion])
        ]);

        $formulario->handleRequest($request);

        if ($formulario->isValid()) {

            /**
             * Se comprueba que cumpla los requisitos para ponerla activa
             * 1. Tiene que tener segmentos asociados
             * 2. Tiene que tener plantilla asociada con gruposdeSlots
             */
            if (Comunicacion::ESTADO_ACTIVO == $comunicacion->getEstado()) {

                if ($comunicacion->getSegmentos()->isEmpty()) {
                    $this->get('session')->getFlashBag()->add('formulario', "mensaje.error.faltan.segmentos");
                    $comunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);
                }

                if ($comunicacion->getGruposSlots()->isEmpty()) {
                    $this->get('session')->getFlashBag()->add('formulario', "mensaje.error.faltan.gruposslot");
                    $comunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);
                }

                if ($comunicacion->getSegmentos()->count() > 0 && $comunicacion->getGruposSlots()->count() > 0) {
                    $this->get('session')->getFlashBag()->add('formulario_ok', "mensaje.ok.guardar");
                }

                $em->persist($comunicacion);
                $em->flush();

                return $this->redirectToRoute('direct_manager_edit_datos', ['idComunicacion' => $idComunicacion]);
            } else {

                if(is_null($comunicacion->getEstado())) {
                    $comunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);
                }

                $em->persist($comunicacion);
                $em->flush();
                $this->get('session')->getFlashBag()->add('formulario_ok', "mensaje.ok.guardar");
            }
        }

        return $this->render('RMComunicacionBundle:Comunicacion:edit.html.twig', [
            'id_comunicacion' => $idComunicacion,
            'formulario'      => $formulario->createView(),
            'objComunicacion' => $comunicacion,
        ]);
    }

    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return JsonResponse
     */
    public function pararAction(Request $request, $idComunicacion)
    {

        $translator = $this->get('translator');

        if (!$idComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', 500);
        }

        $servicioComunicacion = $this->get('comunicacionservice');

        if (!$servicioComunicacion->pararComunicacion($idComunicacion)) {
            return Response::create($translator->trans('mensaje.error.parar.comunicacion'), 500);
        }


        $mensaje      = $translator->trans('mensaje.ok.comunicacion.parada');
        $comunicacion = $this
            ->getManager()
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findById($idComunicacion);

        $html = $this->renderView('@RMComunicacion/Comunicacion/row.html.twig',
            [
                'objComu' => $comunicacion
            ]
        );

        $respuesta = [
            'fila'    => $html,
            'mensaje' => $mensaje
        ];

        return new JsonResponse($respuesta);

    }

    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return JsonResponse
     */
    public function reanudarAction(Request $request, $idComunicacion)
    {
        $translator = $this->get('translator');

        if (!$idComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', 500);
        }

        $servicioComunicacion = $this->get('comunicacionservice');

        if (!$servicioComunicacion->reanudarComunicacion($idComunicacion)) {
            return Response::create($translator->trans('mensaje.error.reanudar.comunicacion'), 500);
        }

        $mensaje = $translator->trans('mensaje.ok.comunicacion.reanudada');

        $comunicacion = $this
            ->getManager()
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findById($idComunicacion);

        $html = $this->renderView('@RMComunicacion/Comunicacion/row.html.twig',
            [
                'objComu' => $comunicacion
            ]
        );

        $respuesta = [
            'fila'    => $html,
            'mensaje' => $mensaje
        ];

        return new JsonResponse($respuesta);
    }

    /**
     * @param $idComunicacion
     *
     * @return Response
     */
    public function editPlantillaComunicacionAction($idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->findById($idComunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            $this->get('session')->getFlashBag()->add('error', 'mensaje.error.no.plantilla');
            return $this->redirectToRoute('rm_plantilla_plantilla_index', ['idComunicacion' => $idComunicacion]);
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

    /**
     * @param Request $request
     * @param         $id_comunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function asignaNuevaPlantillaAction(Request $request, $id_comunicacion)
    {

        $em = $this->getManager();

        $comunicacion = $em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findById($id_comunicacion);

        if(! $comunicacion instanceof Comunicacion) {
            throw $this->createNotFoundException('No se ha encontrado la comunicacion');
        }

        $plantilla = $comunicacion->getPlantilla();

        if ($plantilla instanceof Plantilla &&  $plantilla->getEsModelo()) {
            $this->get('event_dispatcher')->dispatch(ComunicacionEvents::NUEVA_COMUNICACION, new ComunicacionEvent($comunicacion));
        }

        return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', ['idComunicacion' => $id_comunicacion]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteComunicacionesAction(Request $request)
    {
        if (! $request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('Se ha producido un error de envio de la información');
        }

        $servicioCom            = $this->getManager()->getRepository('RMComunicacionBundle:Comunicacion');
        $id_canal               = $request->get('canal');
        $estado                 = $request->get('estado');
        $idComunicacionesBorrar = $request->get('eliminar');

        $servicioCom->deleteComunicaciones($idComunicacionesBorrar);

        $comunicaciones = $servicioCom->findByCanalYEstado($id_canal, $estado);

        return $this->render('RMComunicacionBundle:Comunicacion:list.html.twig', [
            'comunicaciones' => $comunicaciones
        ]);
    }


} 