<?php

namespace RM\InsightBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const RES_MENSUAL = 'rm.mongo.resultado_mensual';
    const RES_CLIENTE = 'rm.mongo.resultado_cliente';

    public function indexAction(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        list($meses, $mes1, $mes2) =  $this->handleMesesDisponibles($request, self::RES_MENSUAL);

        $resultado1 = $dm->getRepository('RMMongoBundle:ResultadoMensual')->findOneBy(['id' => $mes1]);
        $resultado2 = $dm->getRepository('RMMongoBundle:ResultadoMensual')->findOneBy(['id' => $mes2]);

        if (is_null($resultado1) && !is_null($resultado2)) {
            $resultado1 = $resultado2;
        } elseif (is_null($resultado2) && !is_null($resultado1)) {
            $resultado2 = $resultado1;
        } elseif ($resultado1->getFecha() > $resultado2->getFecha()) {
            $aux = $resultado1;
            $resultado1 = $resultado2;
            $resultado2 = $aux;
        }

        $graficaPorcentajeVentas1 =
            $this->get('rm_insight.porcentaje_ventas')
                ->getGraficoPorcentajeVentas($resultado1, 'porcentajeVentas1');

        $graficaPorcentajeVentas2 =
            $this->get('rm_insight.porcentaje_ventas')
                ->getGraficoPorcentajeVentas($resultado2, 'porcentajeVentas2');

        $doceUltimosMeses = $dm->getRepository('RMMongoBundle:ResultadoMensual')->find12UltimosMeses();

        $graficaContribuciones =
            $this->get('rm_insight.numero_contribuciones_vs_numero_miembros')
                ->getGraficoContribucionesClienteVsMiembros($doceUltimosMeses);

        $nivelAmplitud = $this->get('rm.manager')->getManager()
            ->getRepository('RMDiscretasBundle:Configuracion')
            ->findNivelGamaASegmentar();


        return $this->render('RMInsightBundle:Clientes:index.html.twig', [
            'meses'                   => $meses,
            'mes1'                    => $resultado1,
            'mes2'                    => $resultado2,
            'nivelAmplitud'           => $nivelAmplitud,
            'porcentajeVentas1'       => $graficaPorcentajeVentas1,
            'porcentajeVentas2'       => $graficaPorcentajeVentas2,
            'contribucionesMensuales' => $graficaContribuciones
        ]);

    }

    public function performanceAction(Request $request)
    {
        list($meses, $mes1, $mes2) =  $this->handleMesesDisponibles($request);

        $estructura_segmentos = $this->container->getParameter('estrucutra_segmentos_tabla_evolucion');

        $resultado = $this->get('rm_insight.tabla_rendimiento')
            ->tablaRendimiento([$mes1, $mes2], $estructura_segmentos);

        return $this->render('RMInsightBundle:Default:performance.html.twig', [
            'meses'      => $meses,
            'mes1'       => $mes1,
            'mes2'       => $mes2,
            'datos'      => $resultado,
            'estructura' => $estructura_segmentos,
        ]);
    }

    public function evolutionAction(Request $request)
    {
        list($meses, $mes1, $mes2) =  $this->handleMesesDisponibles($request);

        $estructura_segmentos = $this->container->getParameter('estrucutra_segmentos_tabla_evolucion');

        $resultado = $this->get('rm_insight.tabla_rendimiento')
            ->tablaRendimiento([$mes1, $mes2], $estructura_segmentos);

        $grafica = $this->get('rm_insight.evolucion_segmentos')
            ->getGraficoEvolucionSegmentos('grafico');

        return $this->render('RMInsightBundle:Clientes:evolucion.html.twig', [
            'meses'      => $meses,
            'mes1'       => $mes1,
            'mes2'       => $mes2,
            'datos'      => $resultado,
            'grafica'    => $grafica
        ]);
    }

    public function clienteNewAction(Request $request)
    {
        list($meses, $mes1, $mes2) =  $this->handleMesesDisponibles($request);

        $servicio_graficas = $this->get('rm_insight.clientes_nuevos_por_estado_y_segmento');

        $graficaSexo1           = $servicio_graficas->graficaPorSexo([$mes1, $mes2], 'graficoSexos');
        $graficaEdades          = $servicio_graficas->graficoPorEdades([$mes1, $mes2], 'graficoBarrasEdad');
        $graficaFranjaHoraria   = $servicio_graficas->graficoFranjaHoraria([$mes1, $mes2], 'graficoBarrasFranjaHoraria');
        $graficaDias            = $servicio_graficas->graficoDia([$mes1, $mes2], 'graficoBarrasDia');

        return $this->render('RMInsightBundle:Default:clienteNew.html.twig', [
            'fechaInicial'  => 'Ago-2014',
            'fechaFinal'    => 'Dic-2014',
            'meses'         => $meses,
            'mes1'          => $mes1,
            'mes2'          => $mes2,
            'grafiaSexo1'   => $graficaSexo1,
            'graficoEdades' => $graficaEdades,
            'graficoHoras'  => $graficaFranjaHoraria,
            'graficoDias'   => $graficaDias
        ]);
    }

    public function clienteActivoAction(Request $request)
    {

        list($meses, $mes1, $mes2) =  $this->handleMesesDisponibles($request);

        $servicio_graficas = $this->get('rm_insight.clientes_activos_por_estado_y_segmento');

        $graficoRiesgos = $servicio_graficas->graficaEvolucionClientesEnRiesgo('graficoRiesgos');
        $graficaEvolucion = $servicio_graficas->graficaEvolucionActivos('graficoEvolucionActivos');

        $estructura_segmentos = $this->container->getParameter('estrucutra_segmentos_tabla_evolucion');

        if (false === $key = array_search('Estado_Activo', array_keys($estructura_segmentos))) {
            throw new \Exception('No se ha encontrado la estructura de segmentos para el estado Activo');
        }

        $estructura_segmentos_activo = array_slice($estructura_segmentos, $key, 1);

        $resultado = $this->get('rm_insight.tabla_rendimiento')
            ->tablaRendimiento([$mes1, $mes2], $estructura_segmentos_activo);


        return $this->render('RMInsightBundle:Default:clienteActivo.html.twig', [
            'graficoRiesgos'   => $graficoRiesgos,
            'graficaEvolucion' => $graficaEvolucion,
            'mes1'             => $mes1,
            'mes2'             => $mes2,
            'datos'            => $resultado,
            'meses'            => $meses
        ]);
    }

    public function clienteActivoDetalleAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {


        $categories = [
            'Ene',
            'Feb',
            'Mar',
            'Abr',
            'May',
            'Jun',
            'Jul',
            'Ago',
            'Sep',
            'Oct',
            'Nov',
            'Dic'
        ];


        $diferenciales = [
            'difMiembros'   => "9%",
            'difRecencia'   => "14%",
            'difFrecuencia' => "12%",
            'difTicket'     => "23%",
            'difAmplitud'   => "6%"
        ];

        // Se mostrar� la intersecci�n entre los distintos per�odos seleccionadas y los distintos tipos
        $row1 = [
            "Alta",
            "14",
            "15",
            "23"
        ];
        $row2 = [
            "Media",
            "23",
            "16",
            "25"
        ];
        $row3 = [
            "Baja",
            "17",
            "19",
            "13"
        ];

        $countGasto = [
            $row1,
            $row2,
            $row3
        ];

        return $this->render('RMInsightBundle:Default:clienteActivoDetalle.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'countGasto'      => $countGasto,
            'diferenciales'   => $diferenciales,
            'fechaInicial'    => 'Ago-2014',
            'fechaFinal'      => 'Oct-2014',
        ]);
    }

    public function categoriasAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        $ventas = [

            "Ventas",
            "Acumulado Año",
            "190.000",
            "150.000",
            "27",
            "Mes",
            "25.000",
            "30.000",
            "-17",
        ];

        $campañas = [

            "Campañas",
            "Acumulado Año",
            "5",
            "7",
            "-29",
            "Mes",
            "2",
            "1,75",
            "14"
        ];

        $impactos = [

            "Impactos",
            "Acumulado Año",
            "250.000",
            "351.000",
            "-30",
            "Mes",
            "15.000",
            "12.000",
            "25"
        ];

        $redencion = [

            "Redencion",
            "Acumulado Año",
            "2,5",
            "1,75",
            "43",
            "Mes",
            "2,57",
            "1,70",
            "51"
        ];

        $dataTabla = [

            $ventas,
            $campañas,
            $impactos,
            $redencion
        ];


        $row1 = [
            "Carnes",
            "14.000",
            "50%",
            "23%",
            "-2%",
            "4%",
            "14.000",
            "1%"
        ];
        $row2 = [
            "Pescados",
            "8.000",
            "20%",
            "12.3%",
            "5%",
            "-7%",
            "6.500",
            "6%"
        ];
        $row3 = [
            "Frutas",
            "9.560",
            "30%",
            "8%",
            "3%",
            "1%",
            "8.400",
            "3%"
        ];

        $data = [
            $row1,
            $row2,
            $row3
        ];

        return $this->render('RMInsightBundle:Default:categoria.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'number'          => 'N&uacute;mero Promociones Mensuales',
            'data'            => $data,
            'categoria'       => "Frescos",
            'dataTable'       => $dataTabla
        ]);
    }

    public function campaignAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        // Campa�as Instancia Fecha inicio Fecha fin Promociones Target Impactos % Clientes % Slots Ventas
        // Activos 1 01-ene 20 25000 100000 2,75% 688 1,90% 1900 47.500 �
        // Activos 2 01-feb 22 26000 104000 2,80% 728 2,50% 2600 70.200 �
        $row1 = [
            "Activos 1",
            "1",
            "01-ene",
            "20",
            "25000",
            "100000",
            "2,75%",
            "688",
            "1,90%",
            "1900",
            "47.500"
        ];
        $row2 = [
            "Activos 2",
            "2",
            "01-feb",
            "22",
            "26000",
            "100500",
            "2,80%",
            "728",
            "2.50%",
            "2600",
            "70.200"
        ];

        $data = [
            // $row1,
            $row2
        ];

        return $this->render('RMInsightBundle:Default:campaign.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'data'            => $data
        ]);
    }

    public function simulacionAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        // Promociones Clientes Redencion Ventas Coste fijo Coste variable
        // Promo 1 25.734 3% 128.670 � 257 � 6.434 �
        $row1 = [
            "Promo 1",
            "25.734",
            "3%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row2 = [
            "Promo 1",
            "47.734",
            "6%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];

        $data = [
            $row1
            // $row2,
        ];

        $row20 = [
            "Andalucia",
            "30%",
            "20%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row21 = [
            "Aragon",
            "17%",
            "8%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row3 = [
            "Cantabria",
            "12%",
            "3%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row4 = [
            "Castilla y Leon",
            "31%",
            "30%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row5 = [
            "Castilla-La-Mancha",
            "16%",
            "11%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row6 = [
            "Catalu&ntilde;a",
            "56%",
            "60%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row7 = [
            "Ceuta",
            "37%",
            "32%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row8 = [
            "Comunidad de Madrid",
            "44%",
            "46%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row9 = [
            "Comunidad Valenciana",
            "36%",
            "38%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row10 = [
            "Extremadura",
            "47%",
            "46%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row11 = [
            "Galicia",
            "62%",
            "53%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row12 = [
            "Illes Balears",
            "44%",
            "25%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row13 = [
            "Islas Canarias",
            "35%",
            "35%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row14 = [
            "La Rioja",
            "63%",
            "61%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row15 = [
            "Melilla",
            "48%",
            "43%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row16 = [
            "Navarra",
            "31%",
            "24%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row17 = [
            "Pais Vasco",
            "26%",
            "23%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row18 = [
            "Principado de Asturias",
            "67%",
            "67%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row19 = [
            "Region de Murcia",
            "62%",
            "54%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];

        $data2 = [
            $row20,
            $row21,
            $row3,
            $row4,
            $row5,
            $row6,
            $row7,
            $row8,
            $row9,
            $row10,
            $row11,
            $row12,
            $row13,
            $row14,
            $row15,
            $row16,
            $row17,
            $row18,
            $row19
        ];

        return $this->render('RMInsightBundle:Default:simulacion.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'data'            => $data,
            'data2'           => $data2
        ]);
    }

    public function proveedoresAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        // Promociones Clientes Redencion Ventas Coste fijo Coste variable
        // Promo 1 25.734 3% 128.670 � 257 � 6.434 �
        $row1 = [
            "Promo 1",
            "25.734",
            "3%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row2 = [
            "Promo 1",
            "47.734",
            "6%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];

        $data = [
            $row1
            // $row2,
        ];

        $row20 = [
            "Andalucia",
            "30%",
            "20%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row21 = [
            "Aragon",
            "17%",
            "8%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row3 = [
            "Cantabria",
            "12%",
            "3%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row4 = [
            "Castilla y Leon",
            "31%",
            "30%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row5 = [
            "Castilla-La-Mancha",
            "16%",
            "11%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row6 = [
            "Catalu&ntilde;a",
            "56%",
            "60%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row7 = [
            "Ceuta",
            "37%",
            "32%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row8 = [
            "Comunidad de Madrid",
            "44%",
            "46%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row9 = [
            "Comunidad Valenciana",
            "36%",
            "38%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row10 = [
            "Extremadura",
            "47%",
            "46%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row11 = [
            "Galicia",
            "62%",
            "53%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row12 = [
            "Illes Balears",
            "44%",
            "25%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row13 = [
            "Islas Canarias",
            "35%",
            "35%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row14 = [
            "La Rioja",
            "63%",
            "61%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row15 = [
            "Melilla",
            "48%",
            "43%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row16 = [
            "Navarra",
            "31%",
            "24%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row17 = [
            "Pais Vasco",
            "26%",
            "23%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];
        $row18 = [
            "Principado de Asturias",
            "67%",
            "67%",
            "281.875 �",
            "435 �",
            "8.954 �"
        ];
        $row19 = [
            "Region de Murcia",
            "62%",
            "54%",
            "128.670 �",
            "257 �",
            "6.434 �"
        ];

        $data2 = [
            $row20,
            $row21,
            $row3,
            $row4,
            $row5,
            $row6,
            $row7,
            $row8,
            $row9,
            $row10,
            $row11,
            $row12,
            $row13,
            $row14,
            $row15,
            $row16,
            $row17,
            $row18,
            $row19
        ];

        return $this->render('RMInsightBundle:Default:proveedores.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'data'            => $data,
            'data2'           => $data2
        ]);
    }

    protected function handleMesesDisponibles(Request $request, $repository = '')
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        if (self::RES_MENSUAL === $repository) {
            $meses = $dm->getRepository('RMMongoBundle:ResultadoMensual')->findMesesDisponibles();
        } else {
            $meses = $dm->getRepository('RMMongoBundle:ResultadoCliente')->findMesesDisponibles();
        }

        //Por defecto obtiene los resultados de los  2 ultimos meses
        $mes1 = isset($meses[0]['id']) ? $meses[0]['id'] : null;
        $mes2 = isset($meses[1]['id']) ? $meses[1]['id'] : null;

        if(null !== $fecha = $request->get('fecha')){
            $mes1 = $fecha['desde'];
            $mes2 = $fecha['hasta'];
        }

        if (is_null($mes1) && !is_null($mes2)) {
            $mes1 = $mes2;
        } elseif (is_null($mes2) && !is_null($mes1)) {
            $mes2 = $mes1;
        }

        if($mes1 > $mes2) {
            $aux = $mes1;
            $mes1 = $mes2;
            $mes2 = $aux;
        }

        return [$meses, $mes1, $mes2];
    }
}
