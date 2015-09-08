<?php

namespace RM\InsightTrueBundle\Controller;

use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
	
	public function indexAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		$objGT = new Highchart ();
		$objGT->chart->renderTo ( 'graficoTarta' );
		$objGT->title->text ( 'Ventas a Ago-2014' );
		$objGT->plotOptions->pie ( array (
				'allowPointSelect' => true,
				'cursor' => 'pointer',
				'dataLabels' => array (
						'enabled' => false 
				),
				'showInLegend' => true 
		) );
		$data = array (
				array (
						'Miembros',
						8 
				),
				array (
						'No miembros',
						10 
				) 
		);
		$objGT->series ( array (
				array (
						'type' => 'pie',
						'name' => 'Ventas',
						'data' => $data 
				) 
		) );
		
		// Gr�fico columnas
		$arrayValoresRealizadas = array (
				3,
				7,
				6,
				8 
		);
		$arrayValoresTotales = array (
				8,
				7,
				3,
				7 
		);
		
		$series = array (
				
				array (
						'name' => 'Realizadas',
						'type' => 'column',
						'color' => '#4572A7',
						'data' => $arrayValoresRealizadas 
				) 
		);
		
		$categories = array (
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
		);
		
		$objGB = new Highchart ();
		$objGB->chart->renderTo ( 'graficoBarras' ); // The #id of the div where to render the chart
		$objGB->chart->type ( 'column' );
		$objGB->title->text ( 'Contribuciones mensuales vs miembros' );
		$objGB->xAxis->categories ( $categories );
		$objGB->yAxis->min ( '0' );
		$objGB->yAxis->title ( array (
				'text' => "Contribuciones" 
		) );
		$objGB->legend->enabled ( true );
		
		$objGB->series ( $series );
		
		// Datos de palo
		$fechasSelected = array (
				'Ago-2014',
				'Oct-2014' 
		);
		
		$countMiembros = array (
				'Ago-2014' => "175.000",
				'Oct-2014' => "225.000" 
		);
		
		$recenciaDias = array (
				'Ago-2014' => "22",
				'Oct-2014' => "24" 
		);
		
		$frecuenciaMes = array (
				'Ago-2014' => "2,2",
				'Oct-2014' => "2,7" 
		);
		
		$ticketMedio = array (
				'Ago-2014' => "34,3",
				'Oct-2014' => "39,2" 
		);
		
		$amplitudCategorias = array (
				'Ago-2014' => "4,3",
				'Oct-2014' => "3,7" 
		);
		
		$diferenciales = array (
				'difMiembros' => "9",
				'difRecencia' => "14",
				'difFrecuencia' => "-12",
				'difTicket' => "23",
				'difAmplitud' => "-6" 
		);
		
		$row1 = array (
				"Ventas Totales",
				"750.234",
				"725.000",
				"3" 
		);
		$row2 = array (
				"Ventas Miembros",
				"567.900",
				"523.000",
				"8" 
		);
		$row3 = array (
				"Ventas No Miembros",
				"182.334",
				"202.000",
				"-11" 
		);
		$row4 = array (
				"Contribucion Miembros",
				"76%",
				"72%",
				"5" 
		);
		
		$data = array (
				$row1,
				$row2,
				$row3,
				$row4
		);
		
		return $this->render ( 'InsightTrueBundle:Default:index.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'grafico_tarta' => $objGT,
				'grafico_barra' => $objGB,
				'countMiembros' => $countMiembros,
				'recenciaDias' => $recenciaDias,
				'frecuenciaMes' => $frecuenciaMes,
				'ticketMedio' => $ticketMedio,
				'amplitudCategorias' => $amplitudCategorias,
				'diferenciales' => $diferenciales,
				'fecha1' => 'Ago-2014',
				'fecha2' => 'Oct-2014',
				'data' => $data, 
		) );
	}
	
	public function performanceAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		$row1 = array (
				"Nuevos",
				"Nuevo",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-15",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row2 = array (
				"Activos",
				"Fidelizado",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row3 = array (
				"",
				"Habitual",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row4 = array (
				"",
				"Compartido",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row5 = array (
				"",
				"Ocasional",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row6 = array (
				"Inactivos",
				"Inactivo",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		
		$countRendimiento = array (
				$row1,
				$row2,
				$row3,
				$row4,
				$row5,
				$row6 
		);
		
		$diferenciales = array (
				'difMiembros' => "9%",
				'difRecencia' => "14%",
				'difFrecuencia' => "12%",
				'difTicket' => "23%",
				'difAmplitud' => "6%" 
		);
		
		$tipos = array (
				"Nuevo",
				"Fidelizado",
				"Habitual",
				"Compartido",
				"Ocasional",
				"Inactivo" 
		);
		
		return $this->render ( 'InsightTrueBundle:Default:performance.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'rendimiento' => $countRendimiento,
				'diferenciales' => $diferenciales,
				'tipos' => $tipos, 
				'fecha1' => 'Ago-2014',
				'fecha2' => 'Oct-2014',
		) );
	}
	
	public function evolutionAction($idOpcionMenuSup, $idOpcionMenuIzq) {		$row1 = array (
				"Nuevos",
				"Nuevo",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-12",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row2 = array (
				"Activos",
				"Fidelizado",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row3 = array (
				"",
				"Habitual",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row4 = array (
				"",
				"Compartido",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row5 = array (
				"",
				"Ocasional",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row6 = array (
				"Inactivos",
				"Inactivo",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		
		$countRendimiento = array (
				$row1,
				$row2,
				$row3,
				$row4,
				$row5,
				$row6 
		);
		
		$diferenciales = array (
				'difMiembros' => "9%",
				'difRecencia' => "14%",
				'difFrecuencia' => "12%",
				'difTicket' => "23%",
				'difAmplitud' => "6%" 
		);
		
		$tipos = array (
				"Nuevo",
				"Fidelizado",
				"Habitual",
				"Compartido",
				"Ocasional",
				"Inactivo" 
		);
		
		return $this->render ( 'InsightTrueBundle:Default:evolution.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'rendimiento' => $countRendimiento,
				'diferenciales' => $diferenciales,
				'tipos' => $tipos 
		) );
	}
	
	public function clienteActivoAction($idOpcionMenuSup, $idOpcionMenuIzq) {

		$row2 = array (
				"Detalle",
				"Fidelizado",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row3 = array (
				"",
				"Habitual",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"-52",
				"24",
				"87",
				"73" 
		);
		$row4 = array (
				"",
				"Compartido",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		$row5 = array (
				"",
				"Ocasional",
				"145",
				"153",
				"3",
				"15000",
				"16523",
				"15",
				"23",
				"51",
				"61",
				"23,65",
				"22,76",
				"-8",
				"2,4",
				"4.1",
				"52",
				"24",
				"87",
				"73" 
		);
		
		$countRendimiento = array (
				$row2,
				$row3,
				$row4,
				$row5,
		);
		
		$diferenciales = array (
				'difMiembros' => "9%",
				'difRecencia' => "14%",
				'difFrecuencia' => "12%",
				'difTicket' => "23%",
				'difAmplitud' => "6%" 
		);
		
		$tipos = array (
				"Nuevo",
				"Fidelizado",
				"Habitual",
				"Compartido",
				"Ocasional",
				"Inactivo" 
		);
		
		$categoriesRisk = array(
				"Ago",
				"Sep",
				"Oct"
		);
		
		return $this->render ( 'InsightTrueBundle:Default:clienteActivo.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'rendimiento' => $countRendimiento,
				'diferenciales' => $diferenciales,
				'tipos' => $tipos, 
				'fecha1' => 'Ago-2014',
				'fecha2' => 'Oct-2014',
				'categoriesRisk' => $categoriesRisk,
		) );
	}
	
	public function clienteActivoDetalleAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		
		
		$categories = array (
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
		);
		
				
		$diferenciales = array (
				'difMiembros' => "9%",
				'difRecencia' => "14%",
				'difFrecuencia' => "12%",
				'difTicket' => "23%",
				'difAmplitud' => "6%" 
		);
		
		// Se mostrar� la intersecci�n entre los distintos per�odos seleccionadas y los distintos tipos
		$row1 = array (
				"Alta",
				"14",
				"15",
				"23" 
		);
		$row2 = array (
				"Media",
				"23",
				"16",
				"25" 
		);
		$row3 = array (
				"Baja",
				"17",
				"19",
				"13" 
		);
		
		$countGasto = array (
				$row1,
				$row2,
				$row3 
		);
		
		return $this->render ( 'InsightTrueBundle:Default:clienteActivoDetalle.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'countGasto' => $countGasto,
				'diferenciales' => $diferenciales,
				'fechaInicial' => 'Ago-2014',
				'fechaFinal' => 'Oct-2014',
		) );
	}
	
	public function clienteNewAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		$objGT = new Highchart ();
		$objGT->chart->renderTo ( 'graficoTarta' );
		$objGT->title->text ( 'Clientes nuevos por Sexo' );
		$objGT->plotOptions->pie ( array (
				'allowPointSelect' => true,
				'cursor' => 'pointer',
				'dataLabels' => array (
						'enabled' => false 
				),
				'showInLegend' => true 
		) );
		
		$objGT2 = new Highchart ();
		$objGT2->chart->renderTo ( 'graficoTarta' );
		$objGT2->title->text ( 'Clientes nuevos por Sexo' );
		$objGT2->plotOptions->pie ( array (
				'allowPointSelect' => true,
				'cursor' => 'pointer',
				'dataLabels' => array (
						'enabled' => false
				),
				'showInLegend' => true
		) );
		
		$data = array (
				array (
						'Mujer',
						128 
				),
				array (
						'Hombre',
						89 
				) 
		);
		
		$data2 = array (
				array (
						'Jane',
						25
				),
				array (
						'John',
						15
				),
				array (
						'Mike',
						8
				)
		);
		$objGT->series ( array (
				array (
						'type' => 'pie',
						'name' => 'Sexo',
						'data' => $data 
				),
// 				array (
// 						'type' => 'pie',
// 						'name'=> 'Total consumption',
// 						'data' =>$data2
// 				)		
		));
		
		$objGT2->series ( array (
				array (
						'type' => 'pie',
						'name' => 'Sexo',
						'data' => $data2
				),
				// 				array (
						// 						'type' => 'pie',
						// 						'name'=> 'Total consumption',
						// 						'data' =>$data2
						// 				)
		));
		
		return $this->render ( 'InsightTrueBundle:Default:clienteNew.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'fechaInicial' => 'Ago-2014',
				'fechaFinal' => 'Dic-2014',
				//'grafico_tarta' => $objGT,
				//'grafico_tarta2' => $objGT2 
		) );
	}
	
	public function categoriasAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		
		$ventas = array(
			
				"Ventas",
				"Acumulado A�o",
				"190.000",
				"150.000",
				"27",
				"Mes",
				"25.000",
				"30.000",
				"-17",
		);
		
		$campanas = array(
			
				"Campa�as",
				"Acumulado A�o",
				"5",
				"7",
				"-29",
				"Mes",
				"2",
				"1,75",
				"14"
		);
		
		$impactos = array(
			
				"Impactos",
				"Acumulado A�o",
				"250.000",
				"351.000",
				"-30",
				"Mes",
				"15.000",
				"12.000",
				"25"
		);
		
		$redencion = array(
					
				"Redencion",
				"Acumulado A�o",
				"2,5",
				"1,75",
				"43",
				"Mes",
				"2,57",
				"1,70",
				"51"
		);
		
		$dataTabla = array(
			
				$ventas,
				$campanas,
				$impactos,
				$redencion
		);
		
		
		
		$row1 = array (
				"Carnes",
				"14.000",
				"50%",
				"23%",
				"-2%",
				"4%",
				"14.000",
				"1%" 
		);
		$row2 = array (
				"Pescados",
				"8.000",
				"20%",
				"12.3%",
				"5%",
				"-7%",
				"6.500",
				"6%" 
		);
		$row3 = array (
				"Frutas",
				"9.560",
				"30%",
				"8%",
				"3%",
				"1%",
				"8.400",
				"3%" 
		);
		
		$data = array (
				$row1,
				$row2,
				$row3 
		);
		
		return $this->render ( 'InsightTrueBundle:Default:categoria.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'number' => 'N&uacute;mero Promociones Mensuales',
				'data' => $data,
				'categoria' => "Frescos", 
				'dataTable' => $dataTabla
		) );
	}
	
	public function campaignAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		
		// Campa�as Instancia Fecha inicio Fecha fin Promociones Target Impactos % Clientes % Slots Ventas
		// Activos 1 01-ene 20 25000 100000 2,75% 688 1,90% 1900 47.500 �
		// Activos 2 01-feb 22 26000 104000 2,80% 728 2,50% 2600 70.200 �
		$row1 = array (
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
		);
		$row2 = array (
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
		);
		
		$data = array (
				// $row1,
				$row2 
		);
		
		return $this->render ( 'InsightTrueBundle:Default:campaign.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'data' => $data 
		) );
	}
	
	public function simulacionAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		
		// Promociones Clientes Redencion Ventas Coste fijo Coste variable
		// Promo 1 25.734 3% 128.670 � 257 � 6.434 �
		$row1 = array (
				"Promo 1",
				"25.734",
				"3%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row2 = array (
				"Promo 1",
				"47.734",
				"6%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		
		$data = array (
				$row1 
		// $row2,
				);
		
		$row20 = array (
				"Andalucia",
				"30%",
				"20%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row21 = array (
				"Aragon",
				"17%",
				"8%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row3 = array (
				"Cantabria",
				"12%",
				"3%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row4 = array (
				"Castilla y Leon",
				"31%",
				"30%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row5 = array (
				"Castilla-La-Mancha",
				"16%",
				"11%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row6 = array (
				"Catalu&ntilde;a",
				"56%",
				"60%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row7 = array (
				"Ceuta",
				"37%",
				"32%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row8 = array (
				"Comunidad de Madrid",
				"44%",
				"46%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row9 = array (
				"Comunidad Valenciana",
				"36%",
				"38%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row10 = array (
				"Extremadura",
				"47%",
				"46%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row11 = array (
				"Galicia",
				"62%",
				"53%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row12 = array (
				"Illes Balears",
				"44%",
				"25%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row13 = array (
				"Islas Canarias",
				"35%",
				"35%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row14 = array (
				"La Rioja",
				"63%",
				"61%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row15 = array (
				"Melilla",
				"48%",
				"43%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row16 = array (
				"Navarra",
				"31%",
				"24%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row17 = array (
				"Pais Vasco",
				"26%",
				"23%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row18 = array (
				"Principado de Asturias",
				"67%",
				"67%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row19 = array (
				"Region de Murcia",
				"62%",
				"54%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		
		$data2 = array (
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
		);
		return $this->render ( 'InsightTrueBundle:Default:simulacion.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'data' => $data,
				'data2' => $data2 
		) );
	}
	
	public function proveedoresAction($idOpcionMenuSup, $idOpcionMenuIzq) {
		
		// Promociones Clientes Redencion Ventas Coste fijo Coste variable
		// Promo 1 25.734 3% 128.670 � 257 � 6.434 �
		$row1 = array (
				"Promo 1",
				"25.734",
				"3%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row2 = array (
				"Promo 1",
				"47.734",
				"6%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		
		$data = array (
				$row1 
		// $row2,
				);
		
		$row20 = array (
				"Andalucia",
				"30%",
				"20%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row21 = array (
				"Aragon",
				"17%",
				"8%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row3 = array (
				"Cantabria",
				"12%",
				"3%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row4 = array (
				"Castilla y Leon",
				"31%",
				"30%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row5 = array (
				"Castilla-La-Mancha",
				"16%",
				"11%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row6 = array (
				"Catalu&ntilde;a",
				"56%",
				"60%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row7 = array (
				"Ceuta",
				"37%",
				"32%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row8 = array (
				"Comunidad de Madrid",
				"44%",
				"46%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row9 = array (
				"Comunidad Valenciana",
				"36%",
				"38%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row10 = array (
				"Extremadura",
				"47%",
				"46%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row11 = array (
				"Galicia",
				"62%",
				"53%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row12 = array (
				"Illes Balears",
				"44%",
				"25%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row13 = array (
				"Islas Canarias",
				"35%",
				"35%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row14 = array (
				"La Rioja",
				"63%",
				"61%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row15 = array (
				"Melilla",
				"48%",
				"43%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row16 = array (
				"Navarra",
				"31%",
				"24%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row17 = array (
				"Pais Vasco",
				"26%",
				"23%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		$row18 = array (
				"Principado de Asturias",
				"67%",
				"67%",
				"281.875 �",
				"435 �",
				"8.954 �" 
		);
		$row19 = array (
				"Region de Murcia",
				"62%",
				"54%",
				"128.670 �",
				"257 �",
				"6.434 �" 
		);
		
		$data2 = array (
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
		);
		return $this->render ( 'InsightTrueBundle:Default:proveedores.html.twig', array (
				'idOpcionMenuSup' => $idOpcionMenuSup,
				'idOpcionMenuIzq' => $idOpcionMenuIzq,
				'data' => $data,
				'data2' => $data2 
		) );
	}
}
