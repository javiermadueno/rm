<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/05/2015
 * Time: 14:17
 */

namespace RM\InsightBundle\Graphs;


use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\RMMongoBundle\DependencyInjection\EstadisticasClientes;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class NumeroClientesPorEstadoYSegmento
 *
 * @package RM\InsightBundle\Graphs
 */
class ClientesNuevosPorEstadoYSegmentoGraphs extends BaseGraph
{
    /**
     * @var EstadisticasClientes
     */
    private $repository;

    /**
     * @var
     */
    private $em;

    /**
     * @var
     */
    private $segmentoEstado;

    /**
     * @param EstadisticasClientes $repository
     * @param DoctrineManager      $manager
     * @param TranslatorInterface  $translator
     *
     * @throws \Exception
     */
    public function __construct(EstadisticasClientes $repository, DoctrineManager $manager, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->repository = $repository;
        $this->em         = $manager->getManager();
    }


    /**
     * @param        $meses
     * @param string $renderTo
     *
     * @return Highchart
     */
    public function graficaPorSexo($meses, $renderTo = '')
    {
        $segmento_activo = $this->getSegementoEstado();
        $sexo            = $this->getSegmentosSexo();

        if (!$sexo) {
            $chart = $this->graphColumnNoData($renderTo);
            $chart->title->text($this->translator->trans('highchart.insight.nuevos.sexo.title'));
            return $chart;
        }

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_activo),
                array_values($sexo)
            );

        if (!$data) {
            return $this->graphColumnNoData($renderTo);
        }


        $chart = $this->graficoColumnas();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.nuevos.sexo.title'));

        $chart->xAxis->categories( $this->sanitize(array_keys($sexo)) );
        $chart->series($data);

        return $chart;
    }

    public function getSegementoEstado()
    {
        if ($this->segmentoEstado) {
            return $this->segmentoEstado;
        }

        $this->segmentoEstado = $this->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre(['Estado_Nuevo']);

        return $this->segmentoEstado;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosSexo()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosSexo();
    }

    /**
     * @param        $meses
     * @param string $renderTo
     *
     * @return Highchart
     */
    public function graficoPorEdades($meses, $renderTo = '')
    {
        $segmento_activo = $this->getSegementoEstado();
        $edades          = $this->getSegmentosEdades();

        if (!$edades) {
            $chart =  $this->graphColumnNoData($renderTo);
            $chart->title->text($this->translator->trans('highchart.insight.nuevos.edades.title'));
            return $chart;
        }

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_activo),
                array_values($edades)
            );

        if (!$data) {
            return $this->graphNoData();
        }

        $chart = $this->graficoColumnas();

        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.nuevos.edades.title'));


        $chart->xAxis->categories( $this->sanitize(array_keys($edades)));
        $chart->series($data);

        return $chart;

    }

    /**
     * @return array|mixed
     */
    public function getSegmentosEdades()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosEdad();
    }

    /**
     * @param        $meses
     * @param string $renderTo
     *
     * @return Highchart
     */
    public function graficoFranjaHoraria($meses, $renderTo = '')
    {
        $segmento_activo = $this->getSegementoEstado();
        $horas           = $this->getSegmentosFranjaHoraria();

        if(!$horas) {
            $chart =  $this->graphColumnNoData($renderTo);
            $chart->title->text($this->translator->trans('highchart.insight.nuevos.franja.horaria.title'));
            return $chart;
        }

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_activo),
                array_values($horas)
            );

        if (!$data) {
            return $this->graphNoData();
        }

        $chart = $this->graficoColumnas();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.nuevos.franja.horaria.title'));
        $chart->xAxis->categories($this->sanitize(array_keys($horas), 0));
        $chart->series($data);

        return $chart;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosFranjaHoraria()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosFranjaHoraria();
    }

    /**
     * @param        $meses
     * @param string $renderTo
     *
     * @return Highchart
     */
    public function graficoDia($meses, $renderTo = '')
    {
        $segmento_activo = $this->getSegementoEstado();
        $dias            = $this->getSegmentosDias();

        if(!$dias) {
            $chart =  $this->graphColumnNoData($renderTo);
            $chart->title->text($this->translator->trans('highchart.insight.nuevos.dias.title'));
            return $chart;
        }

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_activo),
                array_values($dias)
            );

        if (!$data) {
            return $this->graphNoData();
        }

        $chart = $this->graficoColumnas();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.nuevos.dias.title'));
        $chart->xAxis->categories($this->sanitize(array_keys($dias), 0));
        $chart->series($data);

        return $chart;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosDias()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosPreferenciaDia();
    }

    /**
     * @param        $meses
     * @param string $renderTo
     *
     * @return Highchart
     */
    public function graficoGamas($meses, $renderTo = '')
    {
        $estado_activo = $this->getSegementoEstado();
        $gamas = $this->getSegmentosGamas();

        if(!$gamas) {
            $chart =  $this->graphColumnNoData($renderTo);
            $chart->title->text($this->translator->trans('highchart.insight.nuevos.gama.title'));
            return $chart;
        }

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($estado_activo),
                array_values($gamas)
            );

        if (!$data) {
            return $this->graphNoData();
        }

        $chart = $this->graficoColumnas();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.nuevos.gama.title'));
        $chart->xAxis->categories($this->sanitize(array_keys($gamas), 0));
        $chart->series($data);

        return $chart;
    }

    /**
     * @return array|mixed
     */
    private function getSegmentosGamas()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosGama();
    }



} 