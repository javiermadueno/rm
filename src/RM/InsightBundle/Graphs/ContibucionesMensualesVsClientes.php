<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/04/2015
 * Time: 12:00
 */

namespace RM\InsightBundle\Graphs;

use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\RMMongoBundle\Document\ResultadoMensual;
use Symfony\Component\Translation\TranslatorInterface;
use Zend\Json\Expr;

class ContibucionesMensualesVsClientes
{
    /**
     * @var Highchart
     */
    private $chart;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function getGraficoContribucionesClienteVsMiembros(array $meses)
    {
        $this->chart = new Highchart();

        $series = [];

        /** @var ResultadoMensual $mes */
        foreach ($meses as $mes) {
            $series['contribuciones'][] = is_null($mes->getContribucionClientes()) ? 0 : $mes->getContribucionClientes();
            $series['numeroClientes'][] = is_null($mes->getNumeroClientes()) ? 0 : $mes->getNumeroClientes();
            $series['fechas'][]         = $mes->getFecha()->format('M-Y');
        }

        
        $data  = [

            [
                'name'  => $this->translator->trans('highchart.insight.clientes.numero.contribuciones'),
                'type'  => 'column',
                'yAxis' => 1,
                'data'  => $series['contribuciones']

            ],
            [
                'name'  => $this->translator->trans('highchart.insight.clientes.numero.miembros'),
                'type'  => 'spline',
                'yAxis' => 0,
                'data'  => $series['numeroClientes']
            ],
        ];

        $yData = [
            [
                'title' => [
                    'text'  => $this->translator->trans('highchart.insight.clientes.numero.miembros'),
                ]
            ],
            [
                'labels' => [
                    'formatter' => new Expr('function () { return this.value + " %" }'),
                ],
                'gridLineWidth' => 0,
                'title' => [
                    'text'  => $this->translator->trans('highchart.insight.clientes.numero.contribuciones'),
                ],
                'opposite' => true,
            ],
        ];


        $this->chart->chart->renderTo ( 'contribucionesClientesVsMiembros' );
        $this->chart->chart->type ( 'column' );
        $this->chart->title->text ( $this->translator->trans('highchart.insight.clientes.contribuciones.title') );
        $this->chart->xAxis->categories ( $series['fechas'] );
        $this->chart->yAxis($yData);



        $this->chart->tooltip->shared = true;

        $this->chart->legend->enabled ( false );

        $this->chart->series ( $data );

        return $this->chart;
    }


} 