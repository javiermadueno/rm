<?php

namespace RM\SegmentoBundle\Controller;

use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\ProcesosBundle\Entity\Proceso;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function obtenerSegmentosAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        $em = $this->get('rm.manager')->getManager();

        $servicioSeg   = $this->get("SegmentoService");
        $servicioMarca = $this->get("marcaservice");
        $servicioCat   = $this->get("categoriaService");

        $request = $this->get('request');

        $id_categoria = $request->query->get('id_categoria', -1);
        $id_proveedor = $request->query->get('proveedor', -1);
        $id_variable  = $request->query->get('variables', -1);
        $id_marca     = $request->query->get('id_marca', -1);
        $tipo         = $request->query->get('tipo', -1);

        $tipoVariable = $em->getRepository('RMDiscretasBundle:Tipo')
            ->findOneBy(['codigo' => $tipo]);


        if ($this->get('request')->isXmlHttpRequest()) {
            $segmentos = $servicioSeg
                ->getSegmentosFiltrados($tipoVariable, $id_categoria, $id_marca, $id_proveedor, $id_variable);

            $data = [
                'data'            => $segmentos,
                "recordsTotal"    => intval(count($segmentos)),
                "recordsFiltered" => intval(count($segmentos)),
            ];

            return JsonResponse::create($data, 200);
        }

        $objTipos      = $em->getRepository('RMDiscretasBundle:Tipo')->findAll();
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

    public function showValidarSegmentosAction()
    {
        /*
         * Se instalacia el Entity Manager 'procesos'
         * para poder acceder a la base de datos de procesos
         */
        $em = $this->getDoctrine()->getManager('procesos');

        $repoTipo    = $em->getRepository("ProcesosBundle:TipoProceso");
        $repoEstados = $em->getRepository("ProcesosBundle:EstadoProceso");

        /*
         * Se obtiene el tipo de proceso '0' mediante el su código
         */
        $tipo0 = $repoTipo->findOneBy(['codigo' => 'P00']);

        /*
         * Se obtienene los estados de procesos 'Creado' y 'En Proceso'
         */
        $estadoCreado    = $repoEstados->findOneBy(['codigo' => 'cr']);
        $estadoEnProceso = $repoEstados->findOneBy(['codigo' => 'ep']);

        if (!$tipo0) {
            $this->createNotFoundException('No se ha encontrado tipo 0');
        }

        if (!$estadoCreado) {
            $this->createNotFoundException('No se ha encontrado el estado "Creado"');
        }

        if (!$estadoEnProceso) {
            $this->createNotFoundException('No se ha encontrado el estado "En Proceso"');
        }

        /*
         * Se busca en base de datos si hay algún proceso de la empresa
         * en estado 'Creado' o 'En proceso'. Si hay alguno no se pueden validar
         * los segmentos y se muestra el mensaje 'Validando segmentos'
         */
        $repoProcesos    = $em->getRepository("ProcesosBundle:Proceso");
        $procesoDeCentro = $repoProcesos->findBy([
            'idCentro'      => $this->get('security.context')->getToken()->getUser()->getCliente(),
            'estadoProceso' => [$estadoCreado, $estadoEnProceso],
            'tipoProceso'   => $tipo0
        ]);

        if (count($procesoDeCentro) > 0) {
            return $this->render('RMSegmentoBundle:Default:validandoSegmentos.html.twig');
        } else {
            return $this->render('RMSegmentoBundle:Default:validarSegmentos.html.twig');
        }

    }

    public function validarSegmentosAction()
    {
        $em = $this->getDoctrine()->getManager('procesos');

        $repoTipo    = $em->getRepository("ProcesosBundle:TipoProceso");
        $repoEstados = $em->getRepository("ProcesosBundle:EstadoProceso");

        /*
         * Se obtiene el tipo de proceso '0' mediante el su código
         */
        $tipo0 = $repoTipo->findOneBy(['codigo' => 'P00']);

        /*
         * Se obtienene los estados de procesos 'Creado' y 'En Proceso'
         */
        $estadoCreado = $repoEstados->findOneBy(['codigo' => 'cr']);

        if (!$tipo0) {
            $this->createNotFoundException('No se ha encontrado tipo 0');
        }

        if (!$estadoCreado) {
            $this->createNotFoundException('No se ha encontrado el estado "Creado"');
        }

        $usuario = $this->get('security.context')->getToken()->getUser();

        $proceso = new Proceso();
        $proceso->setFechaCreacion(new \DateTime());
        $proceso->setEstadoProceso($estadoCreado);
        $proceso->setTipoProceso($tipo0);
        $proceso->setUidUsuario($usuario->getUsername());
        $proceso->setIdCentro($usuario->getCliente());

        $em->persist($proceso);
        $em->flush();

        return $this->render('RMSegmentoBundle:Default:validandoSegmentos.html.twig');

    }

    public function searchSegmentosPopoupAction()
    {

        $request = $this->container->get('request');

        $fecha_busqueda = $request->get('fecha_busqueda');
        $fecha_busqueda = new \DateTime($fecha_busqueda);

        return $this->render('RMSegmentoBundle:Default\Buscador:buscadorSegmentosPopup.html.twig', [
            'identificadorSeg' => $request->get('identificadorSeg'),
            'idNombreSeg'      => $request->get('idNombreSeg'),
            'fecha_busqueda'   => $fecha_busqueda
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

        $em = $this->getDoctrine()->getManager($_SESSION['connection']);

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

        if ($id_variable == -1) {
            return Response::create('', 200);
        }

        $em = $this->getDoctrine()->getManager($_SESSION['connection']);

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
