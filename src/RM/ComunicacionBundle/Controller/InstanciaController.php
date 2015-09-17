<?php

namespace RM\ComunicacionBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Campaign;
use RM\ComunicacionBundle\Entity\CampaingCreatividad;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioCom = $this->get("ComunicacionService");
        $servicioSeg = $this->get("SegmentoService");

        $em    = $this->getManager();
        $fases = $em->getRepository('RMComunicacionBundle:Fases')->findAll();

        $objInstancias     = $servicioIC->getInstanciasByFiltro($id_comunicacion, $id_segmento, $fase, $id_instancia);
        $objComunicaciones = $servicioCom->getComunicaciones();

        $objSegmentos = $servicioSeg->getSegmentoByIdComunicacion($id_comunicacion);

        $paginator  = $this->get('knp_paginator');
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
        )
            ;
    }

    /**
     *
     */
    public function actualizarListadoInstanciasAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request       = $this->container->get('request');
            $servicioIC    = $this->get("InstanciaComunicacionService");
            $objInstancias = $servicioIC->getInstanciasByFiltro(
                $request->get('id_comunicacion'),
                $request->get('id_segmento'),
                $request->get('fase'),
                $request->get('id_instancia')
            )
            ;

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate($objInstancias, $request->get('page'), 100);

            return $this->render(
                'RMComunicacionBundle:Instancia:listadoInstancias.html.twig',
                [
                    'objInstancias' => $pagination
                ]
            )
                ;
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

        $em = $this->getManager();

        $instancia = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->findById($id_instancia)
        ;

        $fase = $instancia->getFase();

        switch ($fase->getCodigo()) {
            case InstanciaComunicacion::FASE_CONFIGURACION:
                return $this->faseConfiguracion($instancia);
                break;
            case InstanciaComunicacion::FASE_NEGOCIACION:
                return $this->faseCampaign($instancia);
                break;
            case InstanciaComunicacion::FASE_SIMULACION:
                return $this->faseSimulacion($instancia);
                break;
            case InstanciaComunicacion::FASE_CIERRE:
                return $this->faseCierre($instancia);
                break;
            case InstanciaComunicacion::FASE_GENERACION:
                return $this->faseGeneracion($instancia);
                break;
            case InstanciaComunicacion::FASE_CONFIRMACION:
                return $this->faseConfirmacion($instancia);
                break;
            case InstanciaComunicacion::FASE_FINALIZADA:
                return $this->faseFinalizada($instancia);
                break;
            default:
                throw $this->createNotFoundException();
                break;
        }

    }


    private function faseConfiguracion(InstanciaComunicacion $instancia)
    {
        $servicioIC = $this->get('rm_comunicacion.services.cambio_fase');

        $grupos = $instancia->getGruposSlots()->toArray();

        $tramitar = $servicioIC->compruebaFaseConfiguracion($instancia);

        $faltanNumPromociones = $instancia->isTodosGruposRellenos();
        $faltanGenericas      = $instancia->isTodasGenericasDefinidas();

        return $this->render(
            'RMComunicacionBundle:Instancia:faseConfiguracionPromoNew.html.twig',
            [
                'objInstancia'           => $instancia,
                'objGrupoSlots'          => $grupos,
                'opcionMenuTabFaseConf'  => '1',
                'preview'                => '0',
                'tramitar'               => $tramitar,
                'numPromocionesCorrecto' => $faltanNumPromociones,
                'genericasCorrecto'      => $faltanGenericas
            ]
        )
            ;

    }

    private function faseCampaign(InstanciaComunicacion $instancia)
    {
        $cambioFaseService = $this->get('rm_comunicacion.services.cambio_fase');

        $campaign      = new Campaign($instancia, new ArrayCollection());
        $creatividades = new CampaingCreatividad($instancia, new ArrayCollection());

        $objGT = $this
            ->get('rm_comunicacion.graph.promociones_realizadas_vs_totales')
            ->createPie($instancia, 'graficoTarta')
        ;

        $objGB = $this
            ->get('rm_comunicacion.graph.promociones_realizadas_vs_totales')
            ->createBar($instancia, 'graficoBarras')
        ;

        $tramitar = $cambioFaseService->compruebaFaseCampanya($instancia);

        return $this->render(
            'RMComunicacionBundle:Instancia:fase_campaign.html.twig',
            [
                'objInstancia'  => $instancia,
                'grafico_tarta' => $objGT,
                'grafico_barra' => $objGB,
                'preview'       => '0',
                'tramitar'      => $tramitar,
                'campaign'      => $campaign,
                'creatividad'   => $creatividades
            ]
        )
            ;
    }

    private function faseSimulacion(InstanciaComunicacion $instancia)
    {
        return $this->redirectToRoute('direct_homepage');
    }

    private function faseCierre(InstanciaComunicacion $instancia)
    {
        $cambioFaseService = $this->get('rm_comunicacion.services.cambio_fase');
        $graficas          = $this->get('rm_comunicacion.graphs.promociones_aceptadas_rechazas_pendientes');

        $campaigns     = new Campaign($instancia, new ArrayCollection());
        $creatividades = new CampaingCreatividad($instancia, new ArrayCollection());

        $tramitar        = $cambioFaseService->compruebaFaseCierre($instancia);
        $tieneRechazadas = $cambioFaseService->compruebaPromocionesRechazadasEnFaseCierre($instancia);

        $objGT = $graficas->porcentajePromocionesPorEstado($campaigns);
        $objGB = $graficas->promocionesAceptadasRechazadasPendientesPorGrupoSlot($campaigns);

        return $this->render(
            'RMComunicacionBundle:Instancia:fase_cierre.html.twig',
            [
                'objInstancia'    => $instancia,
                'grafico_tarta'   => $objGT,
                'grafico_barra'   => $objGB,
                'preview'         => '0',
                'tramitar'        => $tramitar,
                'tieneRechazadas' => $tieneRechazadas,
                'campaigns'       => $campaigns,
                'creatividades'   => $creatividades
            ]
        )
            ;
    }

    private function faseGeneracion(InstanciaComunicacion $instancia)
    {
        return $this->redirectToRoute('direct_homepage');
    }

    private function faseConfirmacion(InstanciaComunicacion $instancia)
    {

        return $this->render(
            'RMComunicacionBundle:Instancia:faseConfirmacion.html.twig',
            [
                'objInstancia'      => $instancia,
                'preview'           => '0',
                'tramitar'          => true
            ]
        )
            ;
    }

    private function faseFinalizada(InstanciaComunicacion $instancia)
    {

        $formato = 'XML';
        $server  = 'FTP';
        $user    = 'jorge';
        $pass    = '*****';
        $url     = '192.168.100.1';

        return $this->render(
            'RMComunicacionBundle:Instancia:faseFinalizada.html.twig',
            [
                'objInstancia' => $instancia,
                'preview'      => '0',
                'formato'      => $formato,
                'server'       => $server,
                'user'         => $user,
                'pass'         => $pass,
                'url'          => $url,
                'otros'        => $server
            ]
        )
            ;
    }

    /**
     * @param $listaCatNeeded
     */
    public function fichaAvisosAction($listaCatNeeded)
    {
        $c = 0;

        $listaPrueba      = urldecode($listaCatNeeded);
        $listIdCategorias = json_decode($listaPrueba);


        $servicioCat = $this->get("categoriaservice");
        $servicioIC  = $this->get("InstanciaComunicacionService");

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
                )
                    ;
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

        $encoders    = [
            new JsonEncoder ()
        ];
        $normalizers = [
            new GetSetMethodNormalizer ()
        ];
        $serializer  = new Serializer ($normalizers, $encoders);

        $response = new Response ($serializer->serialize($data, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return Response
     */
    public function envioAvisosAction()
    {
        $request      = $this->container->get('request');
        $mailer       = $this->container->get('mailer');
        $j            = $request->request->get('counter');
        $textoMensaje = $request->request->get('mensaje_mail');

        $arrayMails = [];
        for ($k = 0; $k < $j; $k++) {

            $email = $request->request->get('email_' . $k);

            array_push($arrayMails, $email);
        }

        $message = \Swift_Message::newInstance()->setSubject('Notificacion Instancias')->setFrom(
            'imsodelicioso@gmail.com'
        )->setTo($arrayMails)->setCharset('iso-8859-1')->setContentType("text/html")
        ;

        $message->setBody($textoMensaje);

        $mailer->send($message);

        return Response::create();
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

            $servicioIC    = $this->get("InstanciaComunicacionService");
            $objInstancias = $servicioIC->getInstanciaById($id_instancia);

            if (!$objInstancias) {
                throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
            } else {
                /** @var InstanciaComunicacion $objInstancia */
                $objInstancia = $objInstancias [0];

                $servicioPl  = $this->get("PlantillaService");
                $servicioCat = $this->get("categoriaservice");
                $servicioNP  = $this->get("NumPromocionesService");

                if ($objInstancia->getFase()->getCodigo() == InstanciaComunicacion::FASE_CONFIGURACION) {

                    $comunicacion    = $objInstancia->getIdSegmentoComunicacion()->getIdComunicacion();
                    $id_comunicacion = $comunicacion->getIdComunicacion();
                    $objPlantilla    = $comunicacion->getPlantilla();


                    $objGrupoSlots = $servicioPl->getGruposConNumeroSlots($objPlantilla->getIdPlantilla());

                    if ($request->get('promociones') == 1) {

                        $gruposCreatividades       = array_filter(
                            $objGrupoSlots,
                            function (array $grupo) {
                                return $grupo['tipo'] == GrupoSlots::CREATIVIDADES;
                            }
                        );
                        $objCategorias             = $servicioCat->getCategoriasPorNivelVisible();
                        $objPromociones            = $servicioNP->getNumPromocionesByFiltros(-1, -1, $id_instancia);
                        $objPromocionesCreatividad = $servicioNP->getNumPromocionesCreatividadByFiltros(
                            -1,
                            $id_instancia
                        )
                        ;

                        $servicioIC->guardarFaseConfPromocionesByPost(
                            $objInstancia,
                            $objGrupoSlots,
                            $gruposCreatividades,
                            $objCategorias,
                            $objPromociones,
                            $objPromocionesCreatividad,
                            $request
                        )
                        ;

                        $this->get('session')->getFlashBag()->add('mensaje', 'Registros guardados correctamente');

                        return $this->redirect(
                            $this->generateUrl(
                                'direct_monitor_controlador_fases',
                                [
                                    'id_instancia' => $id_instancia
                                ]
                            )
                        )
                            ;
                    } elseif ($request->get('desempate') == 1) {
                        $em = $this->getManager();

                        $instanciasCriterios = $em->getRepository(
                            'RMProductoBundle:InstanciaCriterioDesempate'
                        )->findBy(
                            [
                                'idInstancia' => $objInstancia
                            ]
                        )
                        ;

                        $criteriosDesempate = $em->getRepository('RMProductoBundle:CriterioDesempate')->findAll();

                        $servicioIC->guardarCriteriosFaseConfiguracion(
                            $objInstancia,
                            $objGrupoSlots,
                            $criteriosDesempate,
                            $instanciasCriterios,
                            $request
                        )
                        ;

                        $this->get('session')->getFlashBag()->add('mensaje', 'Registros guardados correctamente');


                        return $this->redirect(
                            $this->generateUrl(
                                'direct_monitor_fase_conf_criterios',
                                [
                                    'id_instancia' => $id_instancia
                                ]
                            )
                        )
                            ;
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
        )
            ;


    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function controladorFaseConfCriteriosAction($id_instancia)
    {

        $servicioIC    = $this->get("InstanciaComunicacionService");
        $objInstancias = $servicioIC->getInstanciaById($id_instancia);

        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            /** @var InstanciaComunicacion $objInstancia */
            $objInstancia = $objInstancias[0];

            $comunicacion    = $objInstancia->getIdSegmentoComunicacion()->getIdComunicacion();
            $id_comunicacion = $comunicacion->getIdComunicacion();

            $objPlantilla  = $comunicacion->getPlantilla();
            $objGrupoSlots = $this->get('PlantillaService')->getGruposConNumeroSlots($objPlantilla->getIdPlantilla());

            //Recuperamos las instancias de criterios de desempate
            $em                  = $this->getManager();
            $instanciasCriterios = $em->getRepository('RMProductoBundle:InstanciaCriterioDesempate')->findBy(
                [
                    'idInstancia' => $objInstancia
                ]
            )
            ;

            $arrayCriteriosNumSlots = [];
            foreach ($instanciasCriterios as $instancia) {
                $id_grupo                                          = $instancia->getGrupo()->getIdGrupo();
                $tipo_criterio                                     = $instancia->getCriterioDesempate()->getCodigo();
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
            )
                ;

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
        $request           = $this->container->get('request');
        $numComunicaciones = $request->get('numComunicaciones');
        $formato           = $request->get('formato');
        $otros             = $request->get('otros');

        $server = $request->get('server');
        $user   = $request->get('user');
        $pass   = $request->get('pass');

        $url = $request->get('url');

        $fecha = $request->get('fecha');

        $servicioIC    = $this->get("InstanciaComunicacionService");
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
            )
                ;
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
        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioSeg = $this->get("SegmentoService");

        $em = $this->get('rm.manager')->getManager();

        $objInstancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);

        $segmentoComunicacion = $objInstancia->getIdSegmentoComunicacion();
        $id_segmento          = $segmentoComunicacion->getIdSegmento()->getIdSegmento();

        $segmentos = $servicioSeg->getSegmentosInstancia($id_segmento);

        $objConsumidores = null;

        return $this->render(
            'RMComunicacionBundle:Instancia:previsualizacionComunicaciones.html.twig',
            [
                'segmentos'       => $segmentos,
                'objInstancia'    => $objInstancia,
                'objConsumidores' => $objConsumidores
            ]
        )
            ;
    }

    /**
     * @param $id_instancia
     *
     * @return Response
     */
    public function actualizarTablaConsumidoresAction($id_instancia)
    {
        $request     = $this->container->get('request');
        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioCli = $this->get("ClienteService");
        $servicioSeg = $this->get("SegmentoService");
        $idCategoria = $request->get('id_categoria');
        $id_segmento = $request->request->get('segmento');

        $objInstancias = $servicioIC->getInstanciaById($id_instancia);

        // echo '$objInstancias';
        // var_dump($objInstancias);

        $objInstancia = $objInstancias [0];

        if ($id_segmento == -1) {
            $segmentos       = null;
            $objConsumidores = null;
        } else {
            $segmentos       = $servicioSeg->getSegmentosInstancia($id_segmento);
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
        )
            ;
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

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->find($id_instancia);
        $cliente   = $em->getRepository('RMClienteBundle:Cliente')->find($id_cliente);

        $plantilla_path = $this
            ->get('rm_plantilla.email_parser')
            ->parse($instancia, $cliente)
            ->getRutaPlantillaGenerada()
        ;

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

        $cliente = $this->getUser()->getCliente();

        $dm = $this->get('rm.mongo_manager')->getManager();
        $em = $this->get('rm.entity_manager');

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);

        if (!$instancia instanceof InstanciaComunicacion) {
            $this->createNotFoundException(sprintf(
                'No se ha encontrado la instancia con Id = "%s"',
                $id_instancia
            ))
            ;
        }

        $request = $this->get('request');
        $session = $this->get('session');

        $filtro = $request->request->get('filtro', '');
        $filtro = json_decode($filtro, true);

        \MongoCursor::$timeout = -1;

        $key_clientes_session = sprintf('%s_todos_clientes_%s', $cliente, $id_instancia);

        $todos_clientes = $session->get($key_clientes_session);

        if (!$todos_clientes) {
            $clientes = $dm
                ->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
                ->findIdClienteByInstancia($id_instancia)
            ;

            $result = [];
            foreach (array_chunk($clientes, 10000) as $division) {
                $temp = $em
                    ->getRepository('RMClienteBundle:Cliente')
                    ->findClientesByIds($division)
                ;

                $result = array_merge($result, $temp);
            }

            $session->set($key_clientes_session, $result);
            $todos_clientes = $result;
        }

        if (is_array($filtro)) {
            $clientes = $dm
                ->getRepository('RMMongoBundle:ClienteSegmento')
                ->findClientes($filtro)
            ;

            $clientes = $dm
                ->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
                ->findClientesByIdClienteInstancia($clientes, $id_instancia)
            ;

            $filtrado = array_filter(
                $todos_clientes,
                function ($cliente) use ($clientes) {
                    return in_array($cliente['idCliente'], $clientes);
                }
            );

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
        $instanciaServicio = $this->get('rm_comunicacion.services.cambio_fase');

        if (!$instanciaServicio->cambioFase($id_instancia)) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.cambio.fase');

            return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id_instancia]);
        }

        $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.ok.cambio.fase');

        return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id_instancia]);
    }

    public function descargarBatchAction(Request $request, $id)
    {
        $ruta_batch = $this->getParameter('ruta.batch');
        $ruta_batch = $this->getUser()->getCliente() . '/' . $ruta_batch . $id . '.xml';

        $response = new BinaryFileResponse($ruta_batch);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    /**
     * @return mixed
     */
    private function getRefererRoute()
    {
        $request = $this->get('request');

        //look for the referer route
        $referer  = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher    = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route      = $parameters['_route'];

        return $route;
    }
}
