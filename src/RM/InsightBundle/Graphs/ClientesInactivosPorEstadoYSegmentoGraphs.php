<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/08/2015
 * Time: 12:56
 */

namespace RM\InsightBundle\Graphs;

use RM\RMMongoBundle\DependencyInjection\EstadisticasClientes;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\RMMongoBundle\Util;
use Symfony\Component\Translation\TranslatorInterface;



class ClientesInactivosPorEstadoYSegmentoGraphs extends BaseGraph
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
    public function __construct(EstadisticasClientes $repository, DoctrineManager $manager, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->repository = $repository;
        $this->em = $manager->getManager();
    }

    /**
     * @return array
     */
    protected function getSegmentoEstado()
    {
        if ($this->segmentoEstado) {
            return $this->segmentoEstado;
        }

        $this->segmentoEstado = $this->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre(['Estado_Inactivo']);

        return $this->segmentoEstado;
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosEdades()
    {
        return $this->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosEdad();
    }

    /**
     * @return array|mixed
     */
    public function getSegmentosSexo()
    {
        return $this->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosSexo();
    }

    public function graficaEvolucionPorSexo($renderTo = '', \DateTime $from)
    {
        $segmento_estado = $this->getSegmentoEstado();
        $sexo = $this->getSegmentosSexo();
        $meses = Util::getUltimosMeses($from, 12);

        $data =$this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_estado),
                array_values($sexo)
            );

        if (!$data) {
            $graph =  $this->graphColumnNoData($renderTo);
            $graph->title->text($this->translator->trans('highchart.insight.inactivos.evolucion.sexo.title'));
            return $graph;
        }

        $data_prepared = $this->prepareData($data, array_keys($sexo));

        $categorias = $data_prepared['categorias'];
        $series     = $data_prepared['series'];

        $graph = $this->graficoStackColumnas();
        $graph->chart->renderTo($renderTo);
        $graph->title->text($this->translator->trans('highchart.insight.inactivos.evolucion.sexo.title'));
        $graph->xAxis->categories($categorias);
        $graph->series($series);

        return $graph;

    }

    public function graficaEvolucionPorEdad($renderTo = '', \Datetime $from)
    {
        $segmento_estado = $this->getSegmentoEstado();
        $edades = $this->getSegmentosEdades();
        $meses = Util::getUltimosMeses($from, 12);

        $data =$this->repository
            ->findNumeroClientesPorEstadoYPorSegmentos(
                $meses,
                array_values($segmento_estado),
                array_values($edades)
            );

        if (!$data) {
            $graph =  $this->graphColumnNoData($renderTo);
            $graph->title->text($this->translator->trans('highchart.insight.inactivos.evolucion.edades.title'));
            return $graph;
        }

        $data_prepared = $this->prepareData($data, array_keys($edades));

        $categorias = $data_prepared['categorias'];
        $series     = $data_prepared['series'];

        $graph = $this->graficoStackColumnas();
        $graph->chart->renderTo($renderTo);
        $graph->title->text($this->translator->trans('highchart.insight.inactivos.evolucion.edades.title'));
        $graph->xAxis->categories($categorias);
        $graph->series($series);

        return $graph;
    }

} 