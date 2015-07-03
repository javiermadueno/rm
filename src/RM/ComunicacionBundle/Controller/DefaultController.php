<?php

namespace RM\ComunicacionBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Event\ComunicacionEvent;
use RM\ComunicacionBundle\Event\ComunicacionEvents;
use RM\ComunicacionBundle\Form\Gestion\nuevaComunicacionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends RMController
{


    public function indexAction($idOpcionMenuSup)
    {
        return $this->render('RMComunicacionBundle:Default:index.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup
        ]);
    }

    public function showComunicacionesAction($idOpcionMenuSup, $idOpcionMenuIzq, $id_canal = -1, $estado = -2)
    {

        $servicioCanal = $this->get("CanalService");
        $servicioCom   = $this->get("ComunicacionService");

        $selectCanales = $servicioCanal->getCanales();

        $selectComunicaciones = $servicioCom->getComunicaciones($id_canal, $estado);

        /** @var Comunicacion $comunicacion */
        foreach ($selectComunicaciones as $comunicacion) {
            $fechaProxEjecucion = $servicioCom->calculaFechaProximaEjecucion($comunicacion);
            $comunicacion->setFecProximaEjecucion($fechaProxEjecucion);
        }

        return $this->render('RMComunicacionBundle:Default:listado.html.twig', [
            'idOpcionMenuSup'   => $idOpcionMenuSup,
            'idOpcionMenuIzq'   => $idOpcionMenuIzq,
            'objCanales'        => $selectCanales,
            'objComunicaciones' => $selectComunicaciones,
            'id_canal'          => $id_canal,
            'estado'            => $estado
        ]);
    }

    public function actualizarListadoComunicacionesAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request              = $this->container->get('request');
            $servicioCom          = $this->get("ComunicacionService");
            $id_canal             = $request->get('id_canal');
            $estado               = $request->get('estado');
            $selectComunicaciones = $servicioCom->getComunicaciones($id_canal, $estado);

            return $this->render('RMComunicacionBundle:Default:tablaListado.html.twig', [
                'objComunicaciones' => $selectComunicaciones
            ]);
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
        }
    }

    public function deleteComunicacionesAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request                = $this->container->get('request');
            $servicioCom            = $this->get("ComunicacionService");
            $id_canal               = $request->get('id_canal');
            $estado                 = $request->get('estado');
            $idComunicacionesBorrar = $request->get('elementosBorrar');

            $servicioCom->deleteComunicaciones($idComunicacionesBorrar);

            $selectComunicaciones = $servicioCom->getComunicaciones($id_canal, $estado);

            return $this->render('RMComunicacionBundle:Default:tablaListado.html.twig', [
                'objComunicaciones' => $selectComunicaciones
            ]);
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
        }
    }

    public function nuevaComunicacionAction(Request $request, $idOpcionMenuSup, $idOpcionMenuIzq)
    {
        $peticion = $request;

        $objComunicacion = new Comunicacion();
        $objComunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);

        $formulario = $this->createForm(new nuevaComunicacionType(), $objComunicacion);
        $formulario->handleRequest($peticion);

        if ($formulario->isValid()) {

            $em = $this->getManager();

            $objComunicacion->setEstado(Comunicacion::ESTADO_CONFIGURACION);
            $objComunicacion->setGenerada(false);

            $em->persist($objComunicacion);
            $em->flush();

            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ComunicacionEvents::NUEVA_COMUNICACION, new ComunicacionEvent($objComunicacion));

            $id_comunicacion = $objComunicacion->getIdComunicacion();

            return $this->redirect(
                $this->generateUrl('direct_manager_edit_datos',
                    ['idComunicacion' => $id_comunicacion]
                )
            );

        }

        return $this->render('RMComunicacionBundle:Default:nuevaComunicacion.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'formulario'      => $formulario->createView()
        ]);
    }

    public function pararComunicacionAction($idComunicacion)
    {
        $request    = $this->get('request');
        $translator = $this->get('translator');

        if (!$idComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', 500);
        }

        $servicioComunicacion = $this->get('comunicacionservice');

        if (!$servicioComunicacion->pararComunicacion($idComunicacion)) {
            return Response::create($translator->trans('mensaje.error.parar.comunicacion'), 500);
        }


        $mensaje      = $translator->trans('mensaje.ok.comunicacion.parada');
        $comunicacion = $this->getManager()
            ->find('RMComunicacionBundle:Comunicacion', $idComunicacion);

        $html = $this->renderView('@RMComunicacion/Default/filaListadoComunicacion.html.twig',
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

    public function reanudarComunicacionAction($idComunicacion)
    {
        $request    = $this->get('request');
        $translator = $this->get('translator');

        if (!$idComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', 500);
        }

        $servicioComunicacion = $this->get('comunicacionservice');

        if (!$servicioComunicacion->reanudarComunicacion($idComunicacion)) {
            return Response::create($translator->trans('mensaje.error.reanudar.comunicacion'), 500);
        }

        $mensaje = $translator->trans('mensaje.ok.comunicacion.reanudada');

        $comunicacion = $this->getManager()
            ->find('RMComunicacionBundle:Comunicacion', $idComunicacion);

        $html = $this->renderView('@RMComunicacion/Default/filaListadoComunicacion.html.twig',
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

}
