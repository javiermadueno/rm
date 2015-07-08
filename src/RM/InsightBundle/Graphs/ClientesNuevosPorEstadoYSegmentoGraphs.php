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
     *
     * @throws \Exception
     */
    public function __construct(EstadisticasClientes $repository, DoctrineManager $manager, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->repository = $repository;
        $this->em = $manager->getManager();
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
        $sexo = $this->getSegmentosSexo();

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
        $chart->xAxis->categories(array_keys($sexo));
        $chart->series($data);

        return $chart;
    }

    public function getSegementoEstado()
    {
        if ($this->segmentoEstado) {
            return $this->segmentoEstado;
        }

        $this->segmentoEstado = $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre(['Estado_Nuevo']);

        return $this->segmentoEstado;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosSexo()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre([
                'Sexo_Hombre',
                'Sexo_Mujer'
            ]);
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
        $edades = $this->getSegmentosEdades();

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
        $chart->xAxis->categories(array_keys($edades));
        $chart->series($data);

        return $chart;

    }

    /**
     * @return array|mixed
     */
    public function getSegmentosEdades()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre([
                'Fecha de nacimiento_niños',
                'Fecha de nacimiento_adolescentes',
                'Fecha de nacimiento_jovenes',
                'Fecha de nacimiento_jovenes adultos',
                'Fecha de nacimiento_adultos',
                'Fecha de nacimiento_maduros',
                'Fecha de nacimiento_jubilados'
            ]);
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
        $horas = $this->getSegmentosFranjaHoraria();

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
        $chart->xAxis->categories(array_keys($horas));
        $chart->series($data);

        return $chart;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosFranjaHoraria()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre([
                'Franja horaria mañana N últimos meses_Sí',
                'Franja horaria mediodia N últimos meses_Sí',
                'Franja horaria tarde N últimos meses_Sí',
                'Franja horaria noche N últimos meses_Sí',
            ]);
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
        $dias = $this->getSegmentosDias();

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
        $chart->xAxis->categories(array_keys($dias));
        $chart->series($data);

        return $chart;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosDias()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre([
                'Preferencia Fin de Semana N últimos meses_Sí',
                'Preferencia L-J N últimos meses_Sí',
                'Preferencia V N últimos meses_Sí',
            ]);
    }

} 