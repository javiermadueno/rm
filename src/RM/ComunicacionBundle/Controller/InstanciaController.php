<?php

namespace RM\ComunicacionBundle\Controller;

use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Class InstanciaController
 *
 * @package RM\ComunicacionBundle\Controller
 */
class InstanciaController extends RMController
{
    /**
     * @param     $idOpcionMenuSup
     * @param     $idOpcionMenuIzq
     * @param int $id_comunicacion
     * @param int $id_segmento
     * @param int $fase
     * @param int $id_instancia
     *
     * @return Response
     */
    public function showInstanciasAction(
        $idOpcionMenuSup,
        $idOpcionMenuIzq,
        $id_comunicacion = -1,
        $id_segmento = -1,
        $fase = -1,
        $id_instancia = -1
    ) {
        $servicioIC = $this->get("InstanciaComunicacionService");
        $servicioCom = $this->get("ComunicacionService");
        $servicioSeg = $this->get("SegmentoService");

        $em = $this->getManager();
        $fases = $em->getRepository('RMComunicacionBundle:Fases')->findAll();

        $objInstancias = $servicioIC->getInstanciasByFiltro($id_comunicacion, $id_segmento, $fase, $id_instancia);
        $objComunicaciones = $servicioCom->getComunicaciones();

        $objSegmentos = $servicioSeg->getSegmentoByIdComunicacion($id_comunicacion);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($objInstancias, $this->get('request')->query->get('page', 1), 100);


        return $this->render(
            'RMComunicacionBundle:Instancia:index.html.twig',
            [
                'idOpcionMenuSup'   => $idOpcionMenuSup,
                'idOpcionMenuIzq'   => $idOpcionMenuIzq,
                'objComunicaciones' => $objComunicaciones,
                'objSegmentos'      => $objSegmentos,
                'objInstancias'     => $pagination,
                'id_comunicacion'   => $id_comunicacion,
                'id_segmento'       => $id_segmento,
                'fase'              => $fase,
                'id_instancia'      => $id_instancia,
                'fases'             => $fases
            ]
        );
    }

    /**
     *
     */
    public function actualizarListadoInstanciasAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request = $this->container->get('request');
            $servicioIC = $this->get("InstanciaComunicacionService");
            $objInstancias = $servicioIC->getInstanciasByFiltro(
                $request->get('id_comunicacion'),
                $request->get('id_segmento'),
                $request->get('fase'),
                $request->get('id_instancia')
            );

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($objInstancias, $request->get('page'), 100);

            return $this->render(
                'RMComunicacionBundle:Instancia:listadoInstancias.html.twig',
                [
                    'objInstancias' => $pagination
                ]
            );
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la información');
        }
    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function controladorVistasFaseInstanciasAction($id_instancia)
    {
        $servicioIC = $this->get("InstanciaComunicacionService");
        $em = $this->getManager();


        $objInstancias = $servicioIC->getInstanciaById($id_instancia);
        $instancia = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->find($id_instancia);

        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            /** @var InstanciaComunicacion $objInstancia */
            $objInstancia = $objInstancias [0];

            $servicioPl = $this->get("PlantillaService");
            $servicioCat = $this->get("categoriaservice");
            $servicioNP = $this->get("NumPromocionesService");

            $comunicacion = $objInstancia->getIdSegmentoComunicacion()->getIdComunicacion();
            $id_comunicacion = $comunicacion->getIdComunicacion();

            $objPlantilla = $comunicacion->getPlantilla();


            if (!$objPlantilla instanceof Plantilla) {
                $this->get('session')->getFlashBag()->add('mensaje', 'No se ha encontrado la plantilla asociada');
            }


            $objGrupoSlots = $servicioPl->getGruposConNumeroSlots($objPlantilla->getIdPlantilla());

            $gruposPromociones = array_filter($objGrupoSlots, function (array $grupo) {
                    return $grupo['tipo'] == GrupoSlots::PROMOCION;
                }
            );

            $gruposCreatividades = array_filter($objGrupoSlots, function (array $grupo) {
                    return $grupo['tipo'] == GrupoSlots::CREATIVIDADES;
                }
            );

            $objCategorias = $servicioCat->getCatByInstancia($objInstancia->getIdInstancia());

            $fase_instancia = $objInstancia->getFase()->getCodigo();

            if ($fase_instancia == InstanciaComunicacion::FASE_CONFIGURACION) {

                $objPromociones = $servicioNP->getNumPromocionesByFiltros(-1, -1, $id_instancia);
                $objPromocionesCreatividad = $servicioNP->getNumPromocionesCreatividadByFiltros(-1, $id_instancia);

                $objCategorias = $servicioCat->getCategoriasPorNivelVisible();

                $arrayPromoSeg = [];
                $arrayPromoGen = [];
                $arrayPromoSegCreatividad = [];
                $arrayPromoGenCreatividad = [];

                foreach ($objPromociones as $objPromo) {
                    $idGrupo = $objPromo->getIdGrupo()->getIdGrupo();
                    $idCategoria = $objPromo->getIdCategoria()->getIdCategoria();

                    $arrayPromoSeg [$idGrupo] [$idCategoria] = $objPromo->getNumSegmentadas();
                    $arrayPromoGen [$idGrupo] [$idCategoria] = $objPromo->getNumGenericas();
                }

                foreach ($objPromocionesCreatividad as $objPromo) {
                    $idGrupo = $objPromo->getIdGrupo()->getIdGrupo();

                    $arrayPromoSegCreatividad[$idGrupo] = $objPromo->getNumSegmentadas();
                    $arrayPromoGenCreatividad[$idGrupo] = $objPromo->getNumGenericas();
                }

                $tramitar = $servicioIC->compruebaFaseConfiguracion($objInstancia);

                $compruebanNumPromociones = function () use ($objInstancia, $servicioIC) {
                    $grupoSlots = $servicioIC
                        ->findNumRegistrosNumPromocionesPorGrupoSlotsByIdInstancia($objInstancia->getIdInstancia());

                    foreach ($grupoSlots as $grupoSlot) {
                        if (!intval($grupoSlot['numPro'])) {
                            return false;
                        }
                    }

                    return true;
                };

                $em = $this->getManager();

                $totalGenericasPorgrupo = $em->getRepository('RMProductoBundle:NumPromociones')
                    ->findTotalGenericasPorGrupoByInstancia($objInstancia->getIdInstancia());


                $compruebaGenericas = function () use ($totalGenericasPorgrupo) {
                    foreach ($totalGenericasPorgrupo as $total) {
                        $totalGenericas = $total['totalGenericas'];
                        $totalSlots = $total['totalSlots'];

                        if ($totalGenericas < $totalSlots) {
                            return false;
                        }
                    }

                    return true;
                };

                $faltanNumPromociones = $compruebanNumPromociones();
                $faltanGenericas = $compruebaGenericas();

                return $this->render(
                    'RMComunicacionBundle:Instancia:faseConfiguracionPromo.html.twig',
                    [
                        'objInstancia'             => $objInstancia,
                        'objGrupoSlots'            => $objGrupoSlots,
                        'grupoPromociones'         => $gruposPromociones,
                        'grupoCreatividades'       => $gruposCreatividades,
                        'arrayPromoSegCreatividad' => $arrayPromoSegCreatividad,
                        'arrayPromoGenCreatividad' => $arrayPromoGenCreatividad,
                        'objCategorias'            => $objCategorias,
                        'arrayPromoSeg'            => $arrayPromoSeg,
                        'arrayPromoGen'            => $arrayPromoGen,
                        'opcionMenuTabFaseConf'    => '1',
                        'preview'                  => '0',
                        'tramitar'                 => $tramitar,
                        'numPromocionesCorrecto'   => $faltanNumPromociones,
                        'genericasCorrecto'        => $faltanGenericas
                    ]
                );

            } elseif ($fase_instancia == InstanciaComunicacion::FASE_NEGOCIACION) {
                // ECHO 'ENTRO EN FASE 2';
                $objResumenPromociones = $servicioIC->getResumenPromocionesByTipo($id_instancia);

                $arrayInfoPromoTipos = [];
                $arrayInfoPromoCreatividad = [];
                $totalRealizadas = 0;
                $total = 0;
                $grupoTmp = 0;
                $arrayNombreGrupos = [];
                $arrayGrupos = [];
                $arrayValores = []; // De 2 dimensiones, la 1Âª el id del grupo y al segunda puede ser: 1- Realizadas; 2- Totales

                foreach ($objResumenPromociones as $objInfo) {

                    if ($objInfo['tipoGrupo'] == GrupoSlots::PROMOCION) {
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [1] = $objInfo ['numSegmentadas'];
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [2] = $objInfo ['numGenericas'];
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [3] = $objInfo ['num_pro_seg'];
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [4] = $objInfo ['num_pro_gen'];
                    } elseif ($objInfo['tipoGrupo'] == GrupoSlots::CREATIVIDADES) {
                        $arrayInfoPromoCreatividad [$objInfo ['idGrupo']]  [1] = $objInfo ['numSegmentadas'];
                        $arrayInfoPromoCreatividad [$objInfo ['idGrupo']]  [2] = $objInfo ['numGenericas'];
                        $arrayInfoPromoCreatividad [$objInfo ['idGrupo']]  [3] = $objInfo ['num_pro_seg'];
                        $arrayInfoPromoCreatividad [$objInfo ['idGrupo']]  [4] = $objInfo ['num_pro_gen'];
                    }

                    // Valores para los graficos
                    $totalRealizadas += $objInfo ['num_pro_seg'] + $objInfo ['num_pro_gen'];
                    $total += $objInfo ['numSegmentadas'] + $objInfo ['numGenericas'];

                    if ($grupoTmp != $objInfo ['idGrupo']) {
                        array_push($arrayGrupos, $objInfo ['idGrupo']);
                        array_push($arrayNombreGrupos, $objInfo ['nombreGrupo']);

                        $arrayValores [$objInfo ['idGrupo']] [1] = $objInfo ['num_pro_seg'] + $objInfo ['num_pro_gen'];
                        $arrayValores [$objInfo ['idGrupo']] [2] = $objInfo ['numSegmentadas'] + $objInfo ['numGenericas'];
                        $grupoTmp = $objInfo ['idGrupo'];
                    } else {
                        $arrayValores [$objInfo ['idGrupo']] [1] += $objInfo ['num_pro_seg'] + $objInfo ['num_pro_gen'];
                        $arrayValores [$objInfo ['idGrupo']] [2] += $objInfo ['numSegmentadas'] + $objInfo ['numGenericas'];
                    }
                }

                // GrÃ¡fico tarta

                $objGT = new Highchart ();
                $objGT->chart->renderTo('graficoTarta');
                $objGT->title->text('% Promociones elaboradas Global');
                $objGT->plotOptions->pie(
                    [
                        'allowPointSelect' => true,
                        'cursor'           => 'pointer',
                        'dataLabels'       => [
                            'enabled' => false,
                            'format'  => '<b>{point.name}</b>: {point.percentage:.1f} %',
                        ],
                        'showInLegend'     => true
                    ]
                );
                $objGT->tooltip->pointFormat(
                    '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
                );
                $data = [
                    [
                        'Realizadas',
                        $total != 0 ? round(($totalRealizadas / $total) * 100, 2) : 0
                    ],
                    [
                        'Restantes',
                        $total != 0 ? round((($total - $totalRealizadas) / $total) * 100, 2) : 0
                    ]
                ];
                $objGT->series(
                    [
                        [
                            'type' => 'pie',
                            'name' => 'Promociones',
                            'data' => $data
                        ]
                    ]
                );

                // GrÃ¡fico columnas
                $arrayValoresRealizadas = [];
                $arrayValoresTotales = [];

                foreach ($arrayGrupos as $idGrupo) {
                    array_push($arrayValoresRealizadas, $arrayValores [$idGrupo] [1]);
                    array_push($arrayValoresTotales, $arrayValores [$idGrupo] [2]);
                }

                $series = [
                    [
                        'name'  => 'Realizadas',
                        'type'  => 'column',
                        'color' => '#4572A7',
                        'data'  => $arrayValoresRealizadas
                    ],
                    [
                        'name'  => 'Totales',
                        'type'  => 'column',
                        'color' => '#AA4643',
                        'data'  => $arrayValoresTotales
                    ]
                ];

                $categories = $arrayNombreGrupos;

                $objGB = new Highchart ();
                $objGB->chart->renderTo('graficoBarras'); // The #id of the div where to render the chart
                $objGB->chart->type('column');
                $objGB->title->text('Promociones elaboradas vs promociones requeridas');
                $objGB->xAxis->categories($categories);
                $objGB->yAxis->min('0');
                $objGB->yAxis->title(
                    [
                        'text' => "Promociones"
                    ]
                );
                $objGB->legend->enabled(true);

                $objGB->series($series);

                $tramitar = $servicioIC->compruebaFaseCampanya($objInstancia);

                return $this->render(
                    'RMComunicacionBundle:Instancia:faseNegociacion.html.twig',
                    [
                        'objInstancia'               => $objInstancia,
                        'objGrupoSlots'              => $gruposPromociones,
                        'objGrupoSlotsCreatividades' => $gruposCreatividades,
                        'objCategorias'              => $objCategorias,
                        'grafico_tarta'              => $objGT,
                        'grafico_barra'              => $objGB,
                        'arrayInfoPromoTipos'        => $arrayInfoPromoTipos,
                        'arrayInfoPromoCreatividad'  => $arrayInfoPromoCreatividad,
                        'preview'                    => '0',
                        'tramitar'                   => $tramitar
                    ]
                );
            } elseif ($fase_instancia == InstanciaComunicacion::FASE_SIMULACION) {
                return $this->redirect(
                    $this->generateUrl('direct_homepage')
                );
            } elseif ($fase_instancia == InstanciaComunicacion::FASE_CIERRE) {

                $objResumenPromociones = $servicioIC->getResumenPromocionesByEstado($id_instancia);
                $arrayInfoPromoTipos = [];
                $arrayInfoPromoCreatividad = [];
                $arrayEstados = [];
                $arrayEstadosCreatividades = [];
                $arrayNombreGrupos = [];
                $arrayGrupos = [];

                $total = 0;
                $totalPendientes = 0;
                $totalAceptadas = 0;
                $totalRechazadas = 0;
                $grupoTmp = 0;
                foreach ($objResumenPromociones as $objInfo) {

                    $aceptadas = intval($objInfo ['aceptadas']);
                    $pendientes = intval($objInfo ['pendientes']);
                    $rechazadas = intval($objInfo ['rechazadas']);

                    if ($objInfo['tipoGrupo'] == GrupoSlots::PROMOCION) {
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [1] = $aceptadas;
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [2] = $pendientes;
                        $arrayInfoPromoTipos [$objInfo ['idGrupo']] [$objInfo ['idCategoria']] [3] = $rechazadas;

                        if ($pendientes > 0) {
                            $arrayEstados[$objInfo ['idCategoria']] = 'pendiente';
                        } else {
                            $arrayEstados[$objInfo ['idCategoria']] = 'finalizada';
                        }


                    } elseif ($objInfo['tipoGrupo'] == GrupoSlots::CREATIVIDADES) {
                        $arrayInfoPromoCreatividad[$objInfo['idGrupo']][1] = $aceptadas;
                        $arrayInfoPromoCreatividad[$objInfo['idGrupo']][2] = $pendientes;
                        $arrayInfoPromoCreatividad[$objInfo['idGrupo']][3] = $rechazadas;
                        $arrayInfoPromoCreatividad[$objInfo['idGrupo']]['idNumPro'] = $objInfo['idNumPro'];

                        if ($pendientes > 0) {
                            $arrayEstadosCreatividades['idGrupo'] = 'pendiente';
                        }
                    }

                    $total += $aceptadas + $pendientes + $rechazadas;
                    $totalAceptadas += $aceptadas;
                    $totalRechazadas += $rechazadas;
                    $totalPendientes += $pendientes;

                    if ($grupoTmp != $objInfo ['idGrupo']) {
                        array_push($arrayGrupos, $objInfo ['idGrupo']);
                        array_push($arrayNombreGrupos, $objInfo ['nombreGrupo']);

                        $arrayValores [$objInfo ['idGrupo']] [1] = $aceptadas;
                        $arrayValores [$objInfo ['idGrupo']] [2] = $pendientes;
                        $arrayValores [$objInfo ['idGrupo']] [3] = $rechazadas;

                        $grupoTmp = $objInfo ['idGrupo'];
                    } else {
                        $arrayValores [$objInfo ['idGrupo']] [1] += $aceptadas;
                        $arrayValores [$objInfo ['idGrupo']] [2] += $pendientes;
                        $arrayValores [$objInfo ['idGrupo']] [3] += $rechazadas;
                    }
                }

                $creatividades = $this->get('creatividadservice')->getDatosPromocionesCreatividadByInstancia($objInstancia);

                $objGT = new Highchart ();
                $objGT->chart->renderTo('graficoTarta');
                $objGT->title->text('% Promociones elaboradas Global');
                $objGT->plotOptions->pie(
                    [
                        'allowPointSelect' => true,
                        'cursor'           => 'pointer',
                        'dataLabels'       => [
                            'enabled' => false,
                            'format'  => '<b>{point.name}</b>: {point.percentage:.1f} %',
                        ],
                        'showInLegend'     => true
                    ]
                );
                $objGT->tooltip->pointFormat(
                    '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
                );

                $data = [
                    [
                        'name'  => 'Pendientes',
                        'color' => '#e67e22',
                        'y'     => $total != 0 ? round((($totalPendientes) / $total) * 100, 2) : 0
                    ],
                    [
                        'name'  => 'Aceptadas',
                        'color' => '#2ecc71',
                        'y'     => $total != 0 ? round(($totalAceptadas / $total) * 100, 2) : 0
                    ],
                    [
                        'name'  => 'Rechazadas',
                        'color' => '#e74c3c',
                        'y'     => $total != 0 ? round((($totalRechazadas) / $total) * 100, 2) : 0
                    ]
                ];
                $objGT->series(
                    [
                        [
                            'type' => 'pie',
                            'name' => 'Promociones',
                            'data' => $data
                        ]
                    ]
                );

                // GrÃ¡fico columnas
                $arrayValoresAceptadas = [];
                $arrayValoresPendientes = [];
                $arrayValoresRechazadas = [];

                foreach ($arrayGrupos as $idGrupo) {
                    array_push($arrayValoresAceptadas, $arrayValores [$idGrupo] [1]);
                    array_push($arrayValoresPendientes, $arrayValores [$idGrupo] [2]);
                    array_push($arrayValoresRechazadas, $arrayValores [$idGrupo] [3]);
                }

                $series = [
                    [
                        'name'  => 'Pendientes',
                        'type'  => 'column',
                        'color' => '#e67e22',
                        'data'  => $arrayValoresPendientes
                    ],
                    [
                        'name'  => 'Aceptadas',
                        'type'  => 'column',
                        'color' => '#2ecc71',
                        'data'  => $arrayValoresAceptadas
                    ],
                    [
                        'name'  => 'Rechazadas',
                        'type'  => 'column',
                        'color' => '#e74c3c',
                        'data'  => $arrayValoresRechazadas
                    ]
                ];

                $categories = $arrayNombreGrupos;

                $objGB = new Highchart ();
                $objGB->chart->renderTo('graficoBarras'); // The #id of the div where to render the chart
                $objGB->chart->type('column');
                $objGB->title->text('Promociones aceptadas, pendientes, rechazadas');
                $objGB->xAxis->categories($categories);
                $objGB->yAxis->min('0');
                $objGB->yAxis->title(
                    [
                        'text' => "Promociones"
                    ]
                );
                $objGB->legend->enabled(true);

                $objGB->series($series);

                $tramitar = $servicioIC->compruebaFaseCierre($objInstancia);
                $tieneRechazadas = $servicioIC->compruebaPromocionesRechazadasEnFaseCierre($objInstancia);

                return $this->render(
                    'RMComunicacionBundle:Instancia:faseCierreNegociacion.html.twig',
                    [
                        'objInstancia'               => $objInstancia,
                        'objGrupoSlots'              => $gruposPromociones,
                        'objGrupoSlotsCreatividades' => $gruposCreatividades,
                        'objCategorias'              => $objCategorias,
                        'arrayInfoPromoTipos'        => $arrayInfoPromoTipos,
                        'arrayInfoPromoCreatividad'  => $arrayInfoPromoCreatividad,
                        'arrayEstados'               => $arrayEstados,
                        'arrayEstadosCreatividades'  => $arrayEstadosCreatividades,
                        'grafico_tarta'              => $objGT,
                        'grafico_barra'              => $objGB,
                        'preview'                    => '0',
                        'tramitar'                   => $tramitar,
                        'tieneRechazadas'            => $tieneRechazadas,
                        'creatividades'              => $creatividades
                    ]
                );
            } elseif ($fase_instancia == InstanciaComunicacion::FASE_GENERACION) {
                return $this->redirect(
                    $this->generateUrl('direct_homepage')
                );
            } elseif ($fase_instancia == InstanciaComunicacion::FASE_CONFIRMACION) {
                $nomPagFase = 'confirmacion';

                // ECHO 'ENTRO EN FASE 6';

                $numcomunicaciones = '125.548';

                return $this->render(
                    'RMComunicacionBundle:Instancia:faseConfirmacion.html.twig',
                    [
                        'objInstancia'      => $objInstancia,
                        'objGrupoSlots'     => $objGrupoSlots,
                        'objCategorias'     => $objCategorias,
                        'numComunicaciones' => $numcomunicaciones,
                        'preview'           => '0'
                    ]
                );
            } elseif ($fase_instancia == InstanciaComunicacion::FASE_FINALIZADA) {
                $nomPagFase = 'finalizada';

                // ECHO 'ENTRO EN FASE 7';

                $formato = 'XML';
                $server = 'FTP';
                $user = 'jorge';
                $pass = '*****';
                $url = '192.168.100.1';

                return $this->render(
                    'RMComunicacionBundle:Instancia:faseFinalizada.html.twig',
                    [
                        'objInstancia'  => $objInstancia,
                        'objGrupoSlots' => $objGrupoSlots,
                        'objCategorias' => $objCategorias,
                        'preview'       => '0',
                        'formato'       => $formato,
                        'server'        => $server,
                        'user'          => $user,
                        'pass'          => $pass,
                        'url'           => $url,
                        'otros'         => $server
                    ]
                );
            }
        }
    }

    /**
     * @param $listaCatNeeded
     */
    public function fichaAvisosAction($listaCatNeeded)
    {
        $c = 0;

        $servicioPr = $this->get("PromocionService");

        $listaPrueba = urldecode($listaCatNeeded);
        $listIdCategorias = json_decode($listaPrueba);


        $servicioCat = $this->get("CategoriaService");
        $servicioIC = $this->get("InstanciaComunicacionService");

        if (!$listIdCategorias) {
            throw $this->createNotFoundException('No se han encontrado categorï¿½a para filtrar');
        } else {

            $arrayUsers = [];
            foreach ($listIdCategorias as $idCategoria) {

                $categoria = $servicioCat->getCatById($idCategoria);

                $nombreCategoria = $categoria [0]->getNombre();

                $result[$c] = $servicioIC->getCategoryManagersByBusinessCategory($nombreCategoria);

                $c++;
            }

            // echo'Result';
            // TODO: parche para predemo
            $result = [];

            $result[0][0] = "FRESCOS, NARANJA DE MESA";
            $result[0][1] = "Jose Maria Lopez";
            $result[0][2] = "jmlopez@relationalmessages.com";
            $result[0][3] = "914112233";
            $result[1][0] = "REFRIGERADOS, ALIMENTACION, NO ALIMENTACION, SERVICIOS, CONSUMIBLES";
            $result[1][1] = "Jose Maria Perales";
            $result[1][2] = "jmperales@relationalmessages.com";
            $result[1][3] = "914112235";
            $result[2][0] = "LACTEOS, EMBUTIDOS";
            $result[2][1] = "Jose Manuel del Rio";
            $result[2][2] = "jmdelrio@relationalmessages.com";
            $result[2][3] = "914112234";

            //var_dump ( $result );exit(0);

            if ($result != 0) {

                return $this->render(
                    'RMComunicacionBundle:Instancia:fichaAvisos.html.twig',
                    [
                        'objUsers' => $result,
                        'counter'  => count($result)
                    ]
                );
            } else {
                throw $this->createNotFoundException('Se ha producido un error de envio de la informaciï¿½n');
            }
        }
    }

    /**
     * @param $result
     * @param $contador
     *
     * @return Response
     */
    public function showFichaAvisoAction($result, $contador)
    {

        $data = json_encode(
            [
                "resultado" => $result,
                "contador"  => count($result)
            ]
        );

        $encoders = [
            new JsonEncoder ()
        ];
        $normalizers = [
            new GetSetMethodNormalizer ()
        ];
        $serializer = new Serializer ($normalizers, $encoders);

        $response = new Response ($serializer->serialize($data, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return Response
     */
    public function envioAvisosAction()
    {
        $request = $this->container->get('request');
        $mailer = $this->container->get('mailer');
        $j = $request->request->get('counter');
        $textoMensaje = $request->request->get('mensaje_mail');

        $arrayMails = [];
        for ($k = 0; $k < $j; $k++) {

            $email = $request->request->get('email_' . $k);

            array_push($arrayMails, $email);
        }

        $message = \Swift_Message::newInstance()->setSubject('Notificacion Instancias')->setFrom(
            'imsodelicioso@gmail.com'
        )->setTo($arrayMails)->setCharset('iso-8859-1')->setContentType("text/html");

        $message->setBody($textoMensaje);

        $mailer->send($message);

        return $this->render(
            'RMComunicacionBundle:Instancia:faseConfiguracionPromo.html.twig',
            [
                'objInstancia'  => $objInstancia,
                'objGrupoSlots' => $objGrupoSlots,
                'objCategorias' => $objCategorias
            ]
        );
    }

    /**
     * @param         $id_instancia
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function guardarVistasFaseInstanciasAction($id_instancia, Request $request)
    {

        if ($request->isMethod('POST')) {

            $servicioIC = $this->get("InstanciaComunicacionService");
            $objInstancias = $servicioIC->getInstanciaById($id_instancia);

            if (!$objInstancias) {
                throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
            } else {
                /** @var InstanciaComunicacion $objInstancia */
                $objInstancia = $objInstancias [0];

                $servicioPl = $this->get("PlantillaService");
                $servicioCat = $this->get("categoriaservice");
                $servicioNP = $this->get("NumPromocionesService");

                if ($objInstancia->getFase()->getCodigo() == InstanciaComunicacion::FASE_CONFIGURACION) {

                    $comunicacion = $objInstancia->getIdSegmentoComunicacion()->getIdComunicacion();
                    $id_comunicacion = $comunicacion->getIdComunicacion();
                    $objPlantilla = $comunicacion->getPlantilla();


                    $objGrupoSlots = $servicioPl->getGruposConNumeroSlots($objPlantilla->getIdPlantilla());

                    if ($request->get('promociones') == 1) {

                        $gruposCreatividades = array_filter(
                            $objGrupoSlots,
                            function (array $grupo) {
                                return $grupo['tipo'] == GrupoSlots::CREATIVIDADES;
                            }
                        );
                        $objCategorias = $servicioCat->getCategoriasPorNivelVisible();
                        $objPromociones = $servicioNP->getNumPromocionesByFiltros(-1, -1, $id_instancia);
                        $objPromocionesCreatividad = $servicioNP->getNumPromocionesCreatividadByFiltros(
                            -1,
                            $id_instancia
                        );

                        $servicioIC->guardarFaseConfPromocionesByPost(
                            $objInstancia,
                            $objGrupoSlots,
                            $gruposCreatividades,
                            $objCategorias,
                            $objPromociones,
                            $objPromocionesCreatividad,
                            $request
                        );

                        $this->get('session')->getFlashBag()->add('mensaje', 'Registros guardados correctamente');

                        return $this->redirect(
                            $this->generateUrl(
                                'direct_monitor_controlador_fases',
                                ['id_instancia' => $id_instancia]
                            )
                        );

                    } elseif ($request->get('desempate') == 1) {
                        $em = $this->getManager();

                        $instanciasCriterios = $em
                            ->getRepository('RMProductoBundle:InstanciaCriterioDesempate')
                            ->findBy(['idInstancia' => $objInstancia]);

                        $criteriosDesempate = $em
                            ->getRepository('RMProductoBundle:CriterioDesempate')
                            ->findAll();

                        $servicioIC->guardarCriteriosFaseConfiguracion(
                            $objInstancia,
                            $objGrupoSlots,
                            $criteriosDesempate,
                            $instanciasCriterios,
                            $request
                        );

                        $this->get('session')->getFlashBag()->add('mensaje', 'Registros guardados correctamente');


                        return $this->redirect(
                            $this->generateUrl(
                                'direct_monitor_fase_conf_criterios',
                                [
                                    'id_instancia' => $id_instancia
                                ]
                            )
                        );
                    }
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'direct_monitor_controlador_fases',
                [
                    'id_instancia' => $id_instancia
                ]
            )
        );


    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function controladorFaseConfCriteriosAction($id_instancia)
    {

        $servicioIC = $this->get("InstanciaComunicacionService");
        $objInstancias = $servicioIC->getInstanciaById($id_instancia);

        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            /** @var InstanciaComunicacion $objInstancia */
            $objInstancia = $objInstancias[0];

            $comunicacion = $objInstancia->getIdSegmentoComunicacion()->getIdComunicacion();
            $id_comunicacion = $comunicacion->getIdComunicacion();

            $objPlantilla = $comunicacion->getPlantilla();
            $objGrupoSlots = $this->get('PlantillaService')->getGruposConNumeroSlots($objPlantilla->getIdPlantilla());

            //Recuperamos las instancias de criterios de desempate
            $em = $this->getManager();
            $instanciasCriterios = $em->getRepository('RMProductoBundle:InstanciaCriterioDesempate')->findBy(
                [
                    'idInstancia' => $objInstancia
                ]
            );

            $arrayCriteriosNumSlots = [];
            foreach ($instanciasCriterios as $instancia) {
                $id_grupo = $instancia->getGrupo()->getIdGrupo();
                $tipo_criterio = $instancia->getCriterioDesempate()->getCodigo();
                $arrayCriteriosNumSlots[$id_grupo][$tipo_criterio] = $instancia->getNumSlot();
            }

            $criteriosDesempate = $em->getRepository('RMProductoBundle:CriterioDesempate')->findAll();

            $tramitar = $servicioIC->compruebaFaseConfiguracion($objInstancia);

            return $this->render(
                'RMComunicacionBundle:Instancia:faseConfiguracionDesempate.html.twig',
                [
                    'objInstancia'           => $objInstancia,
                    'objGrupoSlots'          => $objGrupoSlots,
                    'arrayCriteriosNumSlots' => $arrayCriteriosNumSlots,
                    'criteriosDesempate'     => $criteriosDesempate,
                    'preview'                => 0,
                    'opcionMenuTabFaseConf'  => 2,
                    'tramitar'               => $tramitar
                ]
            );

        }
    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function finalizarInstanciasAction($id_instancia)
    {

        // TODO Que haga la mecï¿½nica interna para insertar y enviar las notificaciones
        $request = $this->container->get('request');
        $numComunicaciones = $request->get('numComunicaciones');
        $formato = $request->get('formato');
        $otros = $request->get('otros');

        $server = $request->get('server');
        $user = $request->get('user');
        $pass = $request->get('pass');

        $url = $request->get('url');

        $fecha = $request->get('fecha');

        $servicioIC = $this->get("InstanciaComunicacionService");
        $objInstancias = $servicioIC->getInstanciaById($id_instancia);
        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {

            return $this->render(
                'RMComunicacionBundle:Instancia:faseFinalizada.html.twig',
                [
                    'numComunicaciones' => $numComunicaciones,
                    'objInstancia'      => $objInstancias [0],
                    'server'            => $server,
                    'user'              => $user,
                    'pass'              => $pass,
                    'url'               => $url,
                    'formato'           => $formato,
                    'otros'             => $otros,
                    'fecha'             => $fecha
                ]
            );
        }
    }

    /**
     * @param $id_instancia
     *
     * @return Response
     * @throws \Exception
     */
    public function previewComunicacionesAction($id_instancia)
    {
        $servicioIC = $this->get("InstanciaComunicacionService");
        $servicioSeg = $this->get("SegmentoService");

        $em = $this->get('rm.manager')->getManager();

        $objInstancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);

        $segmentoComunicacion = $objInstancia->getIdSegmentoComunicacion();
        $id_segmento = $segmentoComunicacion->getIdSegmento()->getIdSegmento();

        $segmentos = $servicioSeg->getSegmentosInstancia($id_segmento);

        $objConsumidores = null;

        return $this->render(
            'RMComunicacionBundle:Instancia:previsualizacionComunicaciones.html.twig',
            [
                'segmentos'       => $segmentos,
                'objInstancia'    => $objInstancia,
                'objConsumidores' => $objConsumidores
            ]
        );
    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function actualizarTablaConsumidoresAction($id_instancia)
    {
        $request = $this->container->get('request');
        $servicioIC = $this->get("InstanciaComunicacionService");
        $servicioCli = $this->get("ClienteService");
        $servicioSeg = $this->get("SegmentoService");
        $idCategoria = $request->get('id_categoria');
        $id_segmento = $request->request->get('segmento');

        $objInstancias = $servicioIC->getInstanciaById($id_instancia);

        // echo '$objInstancias';
        // var_dump($objInstancias);

        $objInstancia = $objInstancias [0];

        if ($id_segmento == -1) {
            $segmentos = null;
            $objConsumidores = null;
        } else {
            $segmentos = $servicioSeg->getSegmentosInstancia($id_segmento);
            $objConsumidores = $servicioCli->getClientesBySegmento($id_segmento);
        }

        return $this->render(
            'RMComunicacionBundle:Instancia:tablaListadoConsumidores.html.twig',
            [
                'objInstancias'   => $objInstancia,
                'id_categoria'    => $request->get('id_categoria'),
                'segmentos'       => $segmentos,
                'closing'         => $request->get('closing'),
                'objConsumidores' => $objConsumidores
            ]
        );
    }

    /**
     * @param $id_instancia
     * @param $id_cliente
     *
     * @return Response
     * @throws \Exception
     */
    public function showPreviewPromoAction($id_instancia, $id_cliente)
    {

        $em = $this->get('rm.manager')->getManager();
        $dm = $this->get('rm.mongo_manager')->getManager();

        $promocion = $dm->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
            ->findByClienteInstancia($id_cliente, $id_instancia);

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->find($id_instancia);
        $cliente = $em->getRepository('RMClienteBundle:Cliente')->find($id_cliente);

        $plantilla = $instancia->getIdSegmentoComunicacion()->getIdComunicacion()->getPlantilla();

        $plantilla_path = $this->get('rm_plantilla.email_parser')
            ->parse($plantilla, $cliente)
            ->getRutaPlantillaGenerada();

        return new Response(file_get_contents($plantilla_path));

    }

    /**
     * @param $id_instancia
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function gettingConsumersAction($id_instancia)
    {
        $id_instancia = intval($id_instancia);

        $dm = $this->get('rm.mongo_manager')->getManager();
        $em = $this->get('rm.manager')->getManager();

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);

        if (!$instancia instanceof InstanciaComunicacion) {
            $this->createNotFoundException(sprintf(
                'No se ha encontrado la instancia con Id = "%s"',
                $id_instancia
            ));
        }

        $request = $this->get('request');
        $session = $this->get('session');

        $filtro = $request->request->get('filtro', '');
        $filtro = json_decode($filtro, true);

        $todos_clientes = $session->get(sprintf('todos_clientes_%s', $id_instancia));

        if (!$todos_clientes) {
            $clientes = $dm->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
                ->findIdClienteByInstancia($id_instancia);

            $result = [];
            foreach (array_chunk($clientes, 10000) as $division) {
                $temp = $em->getRepository('RMClienteBundle:Cliente')
                    ->findClientesByIds($division);

                $result = array_merge($result, $temp);
            }

            $session->set(sprintf('todos_clientes_%s', $id_instancia), $result);
            $todos_clientes = $result;
        }

        if (is_array($filtro)) {
            $clientes = $dm->getRepository('RMMongoBundle:ClienteSegmento')->findClientes($filtro);
            $clientes = $dm->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
                ->findClientesByIdClienteInstancia($clientes, $id_instancia);

            $filtrado = array_filter($todos_clientes, function ($cliente) use ($clientes) {
                return in_array($cliente['idCliente'], $clientes);
            });

            $filtrado = array_values($filtrado);
        } else {
            $filtrado = $todos_clientes;
        }

        $data = [
            "recordsTotal"    => intval(count($filtrado)),
            "recordsFiltered" => intval(count($filtrado)),
            'data'            => $filtrado
        ];

        return new JsonResponse($data, 200);

    }

    /**
     * @param $id_instancia
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cambioFaseAction($id_instancia)
    {
        $instanciaServicio = $this->get('instanciacomunicacionservice');

        if (!$instanciaServicio->cambioFase($id_instancia)) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.cambio.fase');

            return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id_instancia]);
        }

        $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.ok.cambio.fase');

        return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id_instancia]);
    }

    /**
     * @return mixed
     */
    private function getRefererRoute()
    {
        $request = $this->get('request');

        //look for the referer route
        $referer = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route = $parameters['_route'];

        return $route;
    }
}
