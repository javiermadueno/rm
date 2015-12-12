<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 08/05/2015
 * Time: 11:17
 */

namespace RM\InsightBundle\Graphs;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\RMMongoBundle\DependencyInjection\EstadisticasClientes;
use RM\RMMongoBundle\Util;
use Symfony\Component\Translation\TranslatorInterface;

class EvolucionSegmentosGraph extends BaseGraph
{

    /**
     * @var EstadisticasClientes
     */
    private $repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

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
        $this->em = $manager->getManager();

    }

    public function getGraficoEvolucionSegmentos($renderTo = '', \DateTime $from)
    {
        $estados = $this->getSegmentosEstado();

        $meses = Util::getUltimosMeses($from, 12);

        $data = $this->repository->findNumeroClientosPorSegmentos($meses, array_values($estados));

        $data_prepared = $this->prepareData($data, array_keys($estados));

        $categorias = $data_prepared['categorias'];
        $series     = $data_prepared['series'];

        $graph = $this->graficoStackColumnas();
        $graph->chart->renderTo($renderTo);
        $graph->title->text($this->translator->trans('highchart.insight.evolucion.title'));
        $graph->xAxis->categories($categorias);
        $graph->series($series);

        return $graph;
    }


    public function getSegmentosEstado()
    {
        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosEstado();
    }
} 