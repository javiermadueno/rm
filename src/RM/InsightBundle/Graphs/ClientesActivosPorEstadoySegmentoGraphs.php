<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/06/2015
 * Time: 12:16
 */

namespace RM\InsightBundle\Graphs;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\RMMongoBundle\DependencyInjection\EstadisticasClientes;
use RM\RMMongoBundle\Util;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class ClientesActivosPorEstadoySegmentoGraphs
 *
 * @package RM\InsightBundle\Graphs
 */
class ClientesActivosPorEstadoySegmentoGraphs extends BaseGraph
{
    /**
     * @var array
     */
    private $segmentoEstado;

    /**
     * @var
     */
    private $em;

    /**
     * @var EstadisticasClientes
     */
    private $repository;

    /**
     * @param EstadisticasClientes $repository
     * @param DoctrineManager      $manager
     *
     * @throws \Exception
     */
    public function __construct(
        EstadisticasClientes $repository,
        DoctrineManager $manager,
        TranslatorInterface $translator
    ) {
        parent::__construct($translator);
        $this->repository = $repository;
        $this->em         = $manager->getManager();
    }

    /**
     * @param string $renderTo
     *
     * @return \Ob\HighchartsBundle\Highcharts\Highchart
     */
    public function  graficaEvolucionClientesEnRiesgo($renderTo = '', \DateTime $from)
    {
        $segmento_estado = $this->getSegmentoEstado();
        $riesgo          = $this->getSegmentosRiesgo();
        $meses           = Util::getUltimosMeses($from, 12);

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos($meses, array_values($segmento_estado), array_values($riesgo));

        if (!$data) {
            $graph = $this->graphColumnNoData($renderTo);
            $graph->title->text($this->translator->trans('highchart.insight.activos.evolucion.clientes.riesgo.title'));

            return $graph;
        }

        $data_prepared = $this->prepareData($data, array_keys($riesgo));

        $categorias = $data_prepared['categorias'];
        $series     = $data_prepared['series'];

        $graph = $this->graficoStackColumnas();
        $graph->chart->renderTo($renderTo);
        $graph->title->text($this->translator->trans('highchart.insight.activos.evolucion.clientes.riesgo.title'));
        $graph->xAxis->categories($categorias);
        $graph->series($series);

        return $graph;
    }

    /**
     * @return array
     */
    protected function getSegmentoEstado()
    {
        if ($this->segmentoEstado) {
            return $this->segmentoEstado;
        }

        $this->segmentoEstado = $this->em->getRepository('RMSegmentoBundle:Segmento')
                                         ->findSegmentosByNombre(['Estado_Activo']);

        return $this->segmentoEstado;
    }

    /**
     * @return mixed
     */
    protected function getSegmentosRiesgo()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
                        ->findSegmentosRiesgo();
    }

    /**
     * @param string $renderTo
     *
     * @return \Ob\HighchartsBundle\Highcharts\Highchart
     */
    public function graficaEvolucionActivos($renderTo = '', \DateTime $from)
    {
        $segmento_estado = $this->getSegmentoEstado();
        $activos         = $this->getSegmentosActivos();
        $meses           = Util::getUltimosMeses($from, 12);

        $data = $this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos($meses, array_values($segmento_estado), array_values($activos));

        if (!$data) {
            $graph = $this->graphColumnNoData($renderTo);
            $graph->title->text($this->translator->trans('highchart.insight.activos.evolucion.clientes.activos.title'));

            return $graph;
        }

        $data_prepared = $this->prepareData($data, array_keys($activos));

        $categorias = $data_prepared['categorias'];
        $series     = $data_prepared['series'];

        $graph = $this->graficoStackColumnas();
        $graph->chart->renderTo($renderTo);
        $graph->title->text($this->translator->trans('highchart.insight.activos.evolucion.clientes.activos.title'));
        $graph->xAxis->categories($categorias);
        $graph->series($series);

        return $graph;
    }

    /**
     * @return mixed
     */
    protected function getSegmentosActivos()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
                        ->findSegmentosFidelidad();
    }


}