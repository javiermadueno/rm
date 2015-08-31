<?php

namespace RM\SegmentoBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\TipoRepository;
use RM\DiscretasBundle\Entity\Vid;
use RM\ProcesosBundle\Entity\Proceso;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends RMController
{

    /**
     * @param $idOpcionMenuSup
     * @param $idOpcionMenuIzq
     *
     * @return Response|static
     */
    public function obtenerSegmentosAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {
        $em = $this->getManager();

        $servicioSeg   = $this->get("SegmentoService");
        $servicioMarca = $this->get("marcaservice");

        $request = $this->get('request');

        $fecha_busqueda = $request->query->get('fecha_busqueda', null);
        $id_categoria   = $request->query->get('id_categoria', -1);
        $id_proveedor   = $request->query->get('proveedor', -1);
        $id_variable    = $request->query->get('variables', -1);
        $id_marca       = $request->query->get('id_marca', -1);
        $tipo           = $request->query->get('tipo', -1);

        $tipoVariable = $em->getRepository('RMDiscretasBundle:Tipo')
            ->findOneBy(['codigo' => $tipo]);


        if ($this->get('request')->isXmlHttpRequest()) {
            $segmentos = $servicioSeg
                ->getSegmentosFiltrados($tipoVariable, $id_categoria, $id_marca, $id_proveedor, $id_variable,
                    $fecha_busqueda);

            $data = [
                'data'            => $segmentos,
                "recordsTotal"    => intval(count($segmentos)),
                "recordsFiltered" => intval(count($segmentos)),
            ];

            return JsonResponse::create($data, 200);
        }

        $objTipos      = $em->getRepository('RMDiscretasBundle:Tipo')->findTipoSegmentador();
        $objMarcas     = $servicioMarca->getMarcas();
        $objCategorias = $em->getRepository('RMCategoriaBundle:Categoria')->findCategoriasDeSegmentos();

        return $this->render('RMSegmentoBundle:Default:listado.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'objMarcas'       => $objMarcas,
            'objCategorias'   => $objCategorias,
            'objTipos'        => $objTipos
        ]);
    }


    /**
     * @return Response
     */
    public function showValidarSegmentosAction()
    {
        $em           = $this->getDoctrine()->getManager('procesos');
        $repoProcesos = $em->getRepository("ProcesosBundle:Proceso");

        $procesoDeCentro = $repoProcesos->findProcesosCreadosOEnProceso();

        if (count($procesoDeCentro) > 0) {
            return $this->render('RMSegmentoBundle:Default:validandoSegmentos.html.twig');
        } else {
            return $this->render('RMSegmentoBundle:Default:validarSegmentos.html.twig');
        }
    }

    /**
     * @return Response
     */
    public function validarSegmentosAction()
    {
        $this->get('rm_procesos.factory.proceso_factory')
            ->createProcesoTipo0();

        return $this->render('RMSegmentoBundle:Default:validandoSegmentos.html.twig');
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function searchSegmentosPopoupAction()
    {
        $em = $this->get('rm.manager')->getManager();

        $request = $this->container->get('request');

        $fecha_busqueda = $request->get('fecha_busqueda');
        $fecha_busqueda = new \DateTime($fecha_busqueda);

        $objTipos      = $em->getRepository('RMDiscretasBundle:Tipo')->findTipoSegmentador();
        $objCategorias = $em->getRepository('RMCategoriaBundle:Categoria')->findCategoriasDeSegmentos();

        return $this->render('RMSegmentoBundle:Default\Buscador:buscadorSegmentosPopup.html.twig', [
            'identificadorSeg' => $request->get('identificadorSeg'),
            'idNombreSeg'      => $request->get('idNombreSeg'),
            'fecha_busqueda'   => $fecha_busqueda,
            'objCategorias'    => $objCategorias,
            'objTipos'         => $objTipos
        ]);


    }

    /**
     * Devuelve el listado de variables segun el tipo en formato JSON
     *
     * @param Request $request
     *
     * @return JsonResponse|Response|static
     */
    public function obtenerVariablesAction(Request $request)
    {
        $codigo_tipo = $request->query->get('tipo', -1);

        $em = $this->getManager();

        $tipo = $em->getRepository('RMDiscretasBundle:Tipo')->findOneBy([
            'codigo' => $codigo_tipo
        ]);

        if (!$tipo instanceof Tipo) {
            return new JsonResponse([]);
        }

        $codigo = $tipo->getCodigo();

        switch ($codigo) {
            case Tipo::RFM:
                $variables = $em->getRepository('RMLinealesBundle:Vil')
                    ->findVariablesLinealesByTipo($tipo);
                break;
            case Tipo::COMPRA_PRODUCTO:
            case Tipo::HABITOS_COMPRA:
                $variables = $em->getRepository('RMDiscretasBundle:Vid')
                    ->findVariablesDiscretasByTipo($tipo);
                break;
            case Tipo::CICLO_VIDA:
            case Tipo::OTRAS_TRANSFORMADAS:
                $variables = $em->getRepository('RMTransformadasBundle:Vt')
                    ->obtenerVariablesTransformadas('', $tipo->getId());
                break;
            case Tipo::SOCIODEMOGRAFICO:
                $variables = $this->get('sociodemograficasservice')
                    ->obtenerVariableSociodemograficas();
                break;
            default:
                return JsonResponse::create('Error', 400);

        }

        return new JsonResponse($variables);

    }

    /**
     * Renderiza un select dependiendo de la clasificacion de la variable seleccionda.
     * (Categoria, Marca, Proveedor)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getSelectSegunClaificacionVariableAction(Request $request)
    {
        $id_variable = $request->query->get('id', -1);
        $codigo_tipo = $request->query->get('tipo', -1);

        if ($id_variable === -1) {
            return Response::create('', 200);
        }

        $em = $this->getManager();

        $tipo = $em->getRepository('RMDiscretasBundle:Tipo')->findOneBy([
            'codigo' => $codigo_tipo
        ]);

        if (!$tipo instanceof Tipo) {
            return Response::create('Tipo de variable incorrecto', 400);
        }

        $codigo = $tipo->getCodigo();

        switch ($codigo) {
            case Tipo::RFM:
                $variable = $em->getRepository('RMLinealesBundle:Vil')
                    ->obtenerVLbyId($id_variable);
                break;
            case Tipo::COMPRA_PRODUCTO:
            case Tipo::HABITOS_COMPRA:
                $variable = $em->getRepository('RMDiscretasBundle:Vid')
                    ->obtenerVDbyId($id_variable);
                break;
            case Tipo::CICLO_VIDA:
            case Tipo::OTRAS_TRANSFORMADAS:
                $variable = $em->getRepository('RMTransformadasBundle:Vt')
                    ->obtenerVTbyId($id_variable);
                break;
            case Tipo::SOCIODEMOGRAFICO:
                $variable = $em->getRepository('RMLinealesBundle:Vil')
                    ->obtenerVLbyId($id_variable);
                $variable = empty($variable) ? $em->getRepository('RMDiscretasBundle:Vid')
                    ->obtenerVDbyId($id_variable) : $variable;
                break;
            default:
                return Response::create('Error', 400);

        }

        if (empty($variable) || is_null($variable)) {
            return Response::create(sprintf('No se ha encontrado la variable con id = %s', $id_variable), 400);
        }

        $variable = is_array($variable) ? $variable[0] : $variable;

        if (!$variable instanceof Vid) {
            return Response::create('', 200);
        }

        switch ($variable->getClasificacion()) {
            case Vid::CLASIFICACION_CATEGORIA:
                return $this->render('@RMSegmento/Default/selectClasificacion.html.twig', [
                    'variable'      => $variable,
                    'objCategorias' => $em->getRepository('RMCategoriaBundle:Categoria')->findCategoriasDeSegmentos()
                ]);

            case Vid::CLASIFICACION_MARCA:
                return $this->render('@RMSegmento/Default/selectClasificacion.html.twig', [
                    'variable'  => $variable,
                    'objMarcas' => $this->get('marcaservice')->getMarcas()
                ]);

            case Vid::CLASIFICACION_PROVEEDOR:
                return $this->render('@RMSegmento/Default/selectClasificacion.html.twig', [
                    'variable'    => $variable,
                    'proveedores' => $em->getRepository('RMProductoBundle:Proveedor')->findAll()
                ]);

            default:
                return Response::create('', 200);
        }

    }

}
