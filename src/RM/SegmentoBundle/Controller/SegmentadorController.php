<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/02/2015
 * Time: 10:02
 */

namespace RM\SegmentoBundle\Controller;


use RM\AppBundle\Controller\RMController;
use RM\SegmentoBundle\Entity\SegmentoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

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
        $servicioTransformadas  = $this->get("variablestransformadas");
        $servicioDiscretas      = $this->get("variablesdiscretas");
        $servicioLineales       = $this->get("variableslineales");

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

} 