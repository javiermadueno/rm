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
use Zend\Json\Expr;

class ContibucionesMensualesVsClientes
{
    /**
     * @var Highchart
     */
    private $chart;


    public function getGraficoContribucionesClienteVsMiembros(array $meses)
    {
        $this->chart = new Highchart();

        $series = [];

        /** @var ResultadoMensual $mes */
        foreach ($meses as $mes) {
            $series['contribuciones'][] = is_null($mes->getContribucionClientes()) ? 0 : $mes->getContribucionClientes();
            $series['numeroClientes'][] = is_null($mes->getNumeroClientes()) ? 0 : $mes->getNumeroClientes();
            $series['fechas'][] = $mes->getFecha()->format('M-Y');
        }


        $data = [

            [
                'name'  => 'Numero de contribuciones',
                'type'  => 'column',
                'color' => '#4572A7',
                'yAxis' => 1,
                'data'  => $series['contribuciones']

            ],
            [
                'name'  => 'Numero de miembros',
                'type'  => 'spline',
                'color' => '#AA4643',
                'yAxis' => 0,
                'data'  => $series['numeroClientes']
            ],
        ];

        $yData = [
            [
                'labels' => [
                    'style' => ['color' => '#AA4643']
                ],
                'title'  => [
                    'text'  => 'Numero de miembros',
                    'style' => ['color' => '#AA4643']
                ],
            ],
            [
                'labels'        => [
                    'formatter' => new Expr('function () { return this.value + " %" }'),
                    'style'     => ['color' => '#4572A7']
                ],
                'gridLineWidth' => 0,
                'title'         => [
                    'text'  => 'Numero de contribuciones',
                    'style' => ['color' => '#4572A7']
                ],
                'opposite'      => true,
            ],
        ];


        $this->chart->chart->renderTo('contribucionesClientesVsMiembros');
        $this->chart->chart->type('column');
        $this->chart->title->text('Contribuciones mensuales vs miembros');
        $this->chart->xAxis->categories($series['fechas']);
        $this->chart->yAxis($yData);


        $this->chart->tooltip->shared = true;

        $this->chart->legend->enabled(false);

        $this->chart->series($data);

        return $this->chart;
    }
} 