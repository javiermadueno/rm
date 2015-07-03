<?php

namespace RM\ComunicacionBundle\Controller;

use RM\TransformadasBundle\Entity\Vt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class EdicionController extends Controller
{


    public function nuevoSegmentosComunicacionAction(
        $idOpcionMenuSup,
        $idOpcionMenuIzq,
        $opcionMenuTabComunicacion,
        $id_comunicacion
    ) {
        $servicioCom = $this->get("ComunicacionService");
        $servicioTransformadas = $this->get("variablestransformadas");

        $objComunicaciones = $servicioCom->getComunicacionById($id_comunicacion);
        // 5 Para buscar sólo las variables transformadas de Ciclo de Vida
        $objTransformadas = $servicioTransformadas->getTransformadas('', Vt::TIPO_CICLO_VIDA);

        if (!$objComunicaciones) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            $objComunicacion = $objComunicaciones [0];
            $servicioSegCom = $this->get("SegmentoComunicacionService");
            $objSegmentos = $servicioSegCom->getNuevosSegmentosParaComunicacion($id_comunicacion);

            return $this->render('RMComunicacionBundle:Edicion:nuevoSegmento.html.twig', [
                'idOpcionMenuSup'           => $idOpcionMenuSup,
                'idOpcionMenuIzq'           => $idOpcionMenuIzq,
                'opcionMenuTabComunicacion' => $opcionMenuTabComunicacion,
                'id_comunicacion'           => $id_comunicacion,
                'objSegmentos'              => $objSegmentos,
                'objComunicacion'           => $objComunicacion,
                'objTransformadas'          => $objTransformadas
            ]);
        }
    }


    public function actualizarComboSegmentosAction($id_ciclo_vida)
    {
        $servicioSeg = $this->get("SegmentoService");
        $objSegmentos = $servicioSeg->getSegmentosByIdVt($id_ciclo_vida);

        $encoder = new JsonEncoder ();
        $normalizer = new GetSetMethodNormalizer ();
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getNombre();
        });

        $serializer = new Serializer ([$normalizer], [$encoder]);

        $response = new Response ($serializer->serialize($objSegmentos, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    public function guardarNuevoSegmentosComunicacionAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $servicioSegCom = $this->get("SegmentoComunicacionService");

            $tmpMes = -1;
            $tmpDia = -1;
            if ($request->get('tipo') > 3) {
                $tmpMes = $request->get('mes');
            }
            if ($request->get('tipo') > 1) {
                $tmpDia = $request->get('dia');
            }

            $respuesta = $servicioSegCom->guardarNuevoSegmentoComunicacion(
                -1,
                $request->get('id_comunicacion'),
                $request->get('id_segmento'),
                $request->get('fecIni'),
                $request->get('fecFin'),
                $request->get('estado'),
                $request->get('tipo'),
                $tmpMes,
                $tmpDia,
                $request->get('hora')
            );

            if ($respuesta == 1) {
                $this->get('session')->getFlashBag()->add('mensaje', 'crear_ok');
            } else {
                $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
            }

            return $this->redirect($this->generateUrl('direct_manager_edit_datos', [
                'idComunicacion' => $request->get('id_comunicacion')
            ]));
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
        }
    }


    public function guardarEdicionSegmentosComunicacionAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $servicioSegCom = $this->get("SegmentoComunicacionService");

            $tmpMes = -1;
            $tmpDia = -1;
            if ($request->get('tipo') > 3) {
                $tmpMes = $request->get('mes');
            }
            if ($request->get('tipo') > 1) {
                $tmpDia = $request->get('dia');
            }

            $respuesta = $servicioSegCom->guardarNuevoSegmentoComunicacion($request->get('id_segmento'),
                $request->get('id_comunicacion'), $request->get('id_segmento'), $request->get('fecIni'),
                $request->get('fecFin'), $request->get('estado'), $request->get('tipo'), $tmpMes, $tmpDia,
                $request->get('hora'));

            if ($respuesta == 1) {
                $this->get('session')->getFlashBag()->add('mensaje', 'editar_ok');
            } else {
                $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
            }

            return $this->redirect($this->generateUrl('direct_manager_edit_segmentos_editar', [
                'id_comunicacion' => $request->get('id_comunicacion'),
                'id_segmento'     => $request->get('id_segmento')
            ]));
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
        }
    }

    public function showNuevoMesFrecuenciaAction($valTipo, $valMes, $valDia)
    {
        return $this->render('RMComunicacionBundle:Edicion:mesFrecuencia.html.twig', [
            'valTipo' => $valTipo,
            'valMes'  => $valMes,
            'valDia'  => $valDia
        ]);
    }

    public function showNuevoDiaFrecuenciaAction($valTipo, $valMes, $valDia)
    {
        return $this->render('RMComunicacionBundle:Edicion:diaFrecuencia.html.twig', [
            'valTipo' => $valTipo,
            'valMes'  => $valMes,
            'valDia'  => $valDia
        ]);
    }

    public function edicionSegmentosComunicacionAction(
        $idOpcionMenuSup,
        $idOpcionMenuIzq,
        $opcionMenuTabComunicacion,
        $id_comunicacion,
        $id_segmento
    ) {
        $servicioCom = $this->get("ComunicacionService");

        $objComunicaciones = $servicioCom->getComunicacionById($id_comunicacion);

        if (!$objComunicaciones) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            $servicioSegCom = $this->get("SegmentoComunicacionService");
            $objSegmentos = $servicioSegCom->getSegmentosComunicacionBySC($id_segmento);

            if (!$objSegmentos) {
                throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
            } else {
                $objComunicacion = $objComunicaciones [0];
                $objSegmento = $objSegmentos [0];

                $objSegmentos = $servicioSegCom->getNuevosSegmentosParaComunicacion($id_comunicacion);

                return $this->render('RMComunicacionBundle:Edicion:editarSegmento.html.twig', [
                    'idOpcionMenuSup'           => $idOpcionMenuSup,
                    'idOpcionMenuIzq'           => $idOpcionMenuIzq,
                    'opcionMenuTabComunicacion' => $opcionMenuTabComunicacion,
                    'id_comunicacion'           => $id_comunicacion,
                    'id_segmento'               => $id_segmento,
                    'objSegmentos'              => $objSegmentos,
                    'objSegmento'               => $objSegmento,
                    'objComunicacion'           => $objComunicacion
                ]);
            }
        }
    }
}
