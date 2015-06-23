<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/02/2015
 * Time: 10:02
 */

namespace RM\SegmentoBundle\Controller;


use RM\AppBundle\Controller\RMController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SegmentadorController extends RMController
{
    public function indexAction(Request $request)
    {
        return $this->render('RMSegmentoBundle:Segmentador:buscadorSegmentosPopup.html.twig', [
            'identificadorSeg' => $request->get('identificadorSeg'),
            'idNombreSeg'      => $request->get('idNombreSeg')
        ]);
    }

    public function searchVariablesAction()
    {
        $servicioTransformadas = $this->get("variablestransformadas");
        $servicioDiscretas = $this->get("variablesdiscretas");
        $servicioLineales = $this->get("variableslineales");

        $request = $this->container->get('request');

        $id_tipo_variable = $request->get('tipoSegmento');
        $result = [];
        if ($id_tipo_variable > 0) {

            // Discretas
            if ($id_tipo_variable == 2 || $id_tipo_variable == 3) {

                $result = $servicioDiscretas->getDiscretas('', $id_tipo_variable);
            } // Transformadas
            elseif ($id_tipo_variable == 5 || $id_tipo_variable == 6) {

                $result = $servicioTransformadas->getTransformadas('', $id_tipo_variable);
            } // Lineales
            elseif ($id_tipo_variable == 1) {

                $result = $servicioLineales->getLineales('', $id_tipo_variable);
            } elseif ($id_tipo_variable == 4) {

                $result = $this->get('sociodemograficasservice')->obtenerVariableSociodemograficas();
            }

            $encoders = [
                new JsonEncoder ()
            ];
            $normalizers = [
                new GetSetMethodNormalizer ()
            ];
            $serializer = new Serializer ($normalizers, $encoders);

            $response = new Response ($serializer->serialize($result, 'json'));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    public function searchSegmentosAction()
    {
        $em = $this->getManager();
        $servicioSeg = $this->get("SegmentoService");
        $request = $this->container->get('request');

        $id_variable = $request->get('variable');
        $id_tipo_variable = $request->get('tipoSegmento');

        $fecha_busqueda = $request->get('fecha_busqueda');

        if (!$fecha_busqueda) {
            throw new \Exception('No se ha inidicado la fecha de busqueda');
        }

        $fecha_busqueda = new \DateTime($fecha_busqueda);

        $repo = $em->getRepository('RMSegmentoBundle:Segmento');

        // Discretas
        if ($id_tipo_variable == 2 || $id_tipo_variable == 3) {
            //$result = $servicioSeg->getSegmentosByIdVid($id_variable);
            $result = $repo->findSegmentosByIdVidYFecha($id_variable, $fecha_busqueda);
        } // Transformadas
        elseif ($id_tipo_variable == 5 || $id_tipo_variable == 6) {
            //$result = $servicioSeg->getSegmentosByIdVt($id_variable);
            $result = $repo->findSegmentosByIdVtYFecha($id_variable, $fecha_busqueda);
        } // Lineales
        elseif ($id_tipo_variable == 1) {
            //$result = $servicioSeg->getSegmentosByIdVil($id_variable);
            $result = $repo->findSegmentosByIdVilYFecha($id_variable, $fecha_busqueda);
        } elseif ($id_tipo_variable == 4) {

            $result = $repo->findSegmentosByIdVilYFecha($id_variable, $fecha_busqueda);
            if (empty($result)) {
                $result = $repo->findSegmentosByIdVidSegmentoYFecha($id_variable, $fecha_busqueda);
            }
        }

        $encoders = [
            new JsonEncoder ()
        ];
        $normalizers = [
            new GetSetMethodNormalizer ()
        ];
        $serializer = new Serializer ($normalizers, $encoders);

        $response = new Response ($serializer->serialize($result, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

} 