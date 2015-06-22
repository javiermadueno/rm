<?php

namespace RM\ComunicacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SegmentadorController extends Controller
{
    public function showSegmentadorAvanzadoAction()
    {
        $request = $this->getRequest();
        $origen = $request->get('origen');
        $id_instancia = $request->get('id_instancia');
        $selectedSegment = $request->get('selectedSegment');
        $row = $request->get('row');
        $row = $row == '' ? 0 : $row;
        $check = $request->get('check');


        if ($selectedSegment != null) {

            $_SESSION['listaExpresiones'][$row][2] = $selectedSegment;
        }

        if (isset($_SESSION['listaExpresiones']) and $_SESSION['listaExpresiones'] and $_SESSION['listaExpresiones'] != null) {

            $listaExpresiones = $_SESSION['listaExpresiones'];
            $_SESSION['listaExpresiones'] = null;
        } else {

            $listaExpresiones = null;
        }


        return $this->render('RMComunicacionBundle:Segmentador:showSegmentadorAvanzado.html.twig', [
            'row'              => $row,
            'listaExpresiones' => $listaExpresiones,
            'id_instancia'     => $id_instancia,
            'check'            => $check,
            'selectedSegment'  => $selectedSegment
        ]);
    }

    public function buscarSegmentosAction()
    {
        $servicioIC = $this->get("InstanciaComunicacionService");
        $request = $this->container->get('request');

        $id_instancia = $request->get('id_instancia');
        $row = $request->get('rowId');
        $check = $request->get('selectionNot_' . $row);
        $selectedSegment = $request->get('selectedSegment');

        $numeroRows = $request->get('rowId');


        $listaExpresiones = [];

        for ($i = 0; $i <= $numeroRows; $i++) {

            $expresion = [];

            $selection = $request->get('selectionNot_' . $i);
            $segmento = $request->get('segmento_' . $i);
            $selectionAnd = $request->get('selectionAnd_' . $i);
            $selectionOr = $request->get('selectionOr_' . $i);

            array_push($expresion, $i);
            array_push($expresion, $selection);
            array_push($expresion, $segmento);
            array_push($expresion, $selectionAnd);
            array_push($expresion, $selectionOr);

            array_push($listaExpresiones, $expresion);
        }

        $_SESSION['listaExpresiones'] = $listaExpresiones;


        return $this->render('RMComunicacionBundle:Segmentador:buscarSegmentos.html.twig', [
            'id_instancia'    => $id_instancia,
            'row'             => $row,
            'objSegmentos'    => null,
            'check'           => $check,
            'selectedSegment' => $selectedSegment,
        ]);
    }

    public function obtenerFiltro()
    {

        $request = $this->container->get('request');
        $row = $request->get('rowId');

        $filter = "";

        for ($i = 0; $i <= $row; $i++) {

            $expresion = [];

            $selection = $request->get('selectionNot_' . $i);
            $segmento = $request->get('segmento_' . $i);
            $selectionAnd = $request->get('selectionAnd_' . $i);
            $selectionOr = $request->get('selectionOr_' . $i);

            if ($selection) {

                $filter = $filter . "NO ";
            }
            if ($segmento) {

                $filter = $filter . $segmento;
            }

            if ($i < $row) {
                if ($selectionAnd) {

                    $filter = $filter . " Y ";
                } elseif ($selectionOr) {

                    $filter = $filter . " O ";
                }
            }
        }

        $_SESSION['filtro'] = $filter;

        $response = new Response();
        return $response;
    }

    public function previsualizarFiltroAction($id_instancia)
    {
        // TODO añadir búsqueda de consumidores
        $servicioIC = $this->get("InstanciaComunicacionService");
        $servicioSeg = $this->get("SegmentoService");
        $servicioCli = $this->get("ClienteService");
        $servicioPromo = $this->get("PromocionService");

        $_SESSION['formulario'] = null;


        if (isset($_SESSION['filtro'])) {

            $filtro = $_SESSION['filtro'];
        } else {

            $filtro = null;
        }

        $request = $this->container->get('request');
        $numComunicaciones = $request->get('numComunicaciones');
        $formato = $request->get('formato');
        $otros = $request->get('otros');

        $objConsumidores = null;

        $promo = $servicioPromo->getPromocionById($id_instancia)[0];
        $inicio = $request->get('inicio');
        if ($inicio != 1) {
            $promo->setFiltro($filtro);
            $promo->setPoblacion(rand(1000, 8000));

            $em = $this->getDoctrine()->getManager($_SESSION['connection']);
            $em->persist($promo);
            $em->flush();
        }

        if ($inicio == 1) {
            $filtro = $promo->getFiltro();
            $filtro = $filtro == null ? '' : $filtro;
            unset($_SESSION['filtro']);
        }


        $instancia = $promo->getNumPromocion()->getIdInstancia()->getIdInstancia();

        return $this->redirect($this->generateUrl('campaign_ficha',
                ['id_instancia' => $instancia, 'id_categoria' => 0]));

    }


}
