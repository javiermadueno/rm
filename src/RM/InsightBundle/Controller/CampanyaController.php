<?php

namespace RM\InsightBundle\Controller;


use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Canal;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\InsightBundle\Filter\InstanciaComunicacionFilter;
use RM\InsightBundle\Form\Type\BuscadorCampanyasType;
use Symfony\Component\HttpFoundation\Request;

class CampanyaController extends RMController
{

    public function campaignAction(Request $request)
    {
        $em = $this->getManager();

        $res          = null;
        $evolucion    = null;
        $distribucion = null;
        $fidelizacion = null;
        $sexos        = null;
        $edad         = null;
        $valor        = null;


        $filtro = new InstanciaComunicacionFilter();

        $form = $this->createForm(new BuscadorCampanyasType(), $filtro, [
            'em'     => $em,
            'action' => $this->generateUrl('rm_insight_campaign'),
            'method' => Request::METHOD_POST,
            'comunicacion_requerida' => true
        ])->add('submit', 'submit', ['label' => 'boton.buscar']);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $comunicacion = $filtro->getComunicacion();
            $fechaInicio  = $filtro->getFechaInicio();
            $fechaFin     = $filtro->getFechaFin();

            $res = $this
                ->get('rm_insight.services.estadisticas_campanya')
                ->getResumenCampanya($comunicacion, $fechaInicio, $fechaFin);

            $distribucion = $this
                ->get('rm_insight.services.estadisticas_campanya')
                ->getDistribucionClientesPorSegmentos($comunicacion, $fechaInicio, $fechaFin, []);

            $graficas = $this->get('rm_insight.campanyas.graph');

            $evolucion    = $graficas->graficaReactividadReactanciaVentas($res, "evolucion");
            $fidelizacion = $graficas->getGraficaDistribucionFidelidad($distribucion, 'fidelizacion');
            $sexos        = $graficas->getGraficaDistribucionSexos($distribucion, 'sexos');
            $edad         = $graficas->getGraficaDistribucionEdad($distribucion, 'edad');
            $valor        = $graficas->getGraficaDistribucionValor($distribucion, 'valor');
        }

        return $this->render('RMInsightBundle:Campanya:campaign.html.twig', [
            'form'         => $form->createView(),
            'res'          => $res,
            'evolucion'    => $evolucion,
            'distribucion' => $distribucion,
            'fidelizacion' => $fidelizacion,
            'sexos'        => $sexos,
            'edad'         => $edad,
            'valor'        => $valor
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCampanyaEmailAction(Request $request)
    {
        $em = $this->getManager();

        $canal = $em
            ->getRepository('RMComunicacionBundle:Canal')
            ->findBy(['nombre' => 'Email']);

        $instancias = null;

        $filtro = new InstanciaComunicacionFilter();

        $form = $this->createForm(new BuscadorCampanyasType(), $filtro, [
            'em'     => $em,
            'action' => $this->generateUrl('rm_insight_campaign_email_list'),
            'method' => Request::METHOD_POST,
            'canal'  => $canal instanceof Canal ? $canal->getIdCanal() : null,
        ])->add('submit', 'submit', ['label' => 'boton.buscar']);

        $form->handleRequest($request);

        $instancias = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->findInstanciasEmailByComunicacionYFechas($filtro);


        return $this->render('@RMInsight/Campanya/email_list.html.twig', [
            'form' => $form->createView(),
            'instancias' => $instancias
        ]);
    }

    /**
     * @param Request $request
     * @param         $instancia
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function campanyaEmailAction(Request $request, $instancia)
    {
        $em = $this->getManager();
        $dm = $this->getMongoManager();

        $instancia = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->findById($instancia);

        if(!$instancia instanceof InstanciaComunicacion || $instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_FINALIZADA ) {
            $this->addFlash('error', 'No se ha encontrado la instancia de comunicacion');
            return $this->redirectToRoute('rm_insight_campaign_email_list');
        }


        $res = $this
            ->get('rm_tracking.factory.statistic_factory')
            ->createStatisticsFor($instancia);


        return $this->render('RMInsightBundle:Campanya/Email:email.html.twig', [
            'res' => $res,
            'instancia' => $instancia
        ]);


    }

} 