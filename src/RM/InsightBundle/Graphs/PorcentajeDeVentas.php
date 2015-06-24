<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 22/04/2015
 * Time: 16:16
 */

namespace RM\InsightBundle\Graphs;

use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\RMMongoBundle\Document\ResultadoMensual;


class PorcentajeDeVentas
{
    /**
     * @var Highchart
     */
    private $chart;


    public function getGraficoPorcentajeVentas(ResultadoMensual $mes, $nombre)
    {


        $this->chart = new Highchart();
        $this->chart->chart->renderTo($nombre);

        $this->chart->plotOptions->pie([
            'allowPointSelect' => true,
            'cursor'           => 'pointer',
            'dataLabels'       => [
                'enabled' => false
            ],
            'showInLegend'     => true
        ]);

        if (!$mes) {
            return $this->chart;
        }

        $this->chart->title->text(sprintf('Ventas %s', $mes->getFecha()->format('F-Y')));
        $this->chart->series([
                [
                    'type' => 'pie',
                    'name' => 'Ventas',
                    'data' => [
                        ['Miembros', $mes->getVentasCliente() ? $mes->getVentasCliente() : 0.00],
                        ['No miembros', $mes->getVentasNoCliente() ? $mes->getVentasNoCliente() : 0.00]
                    ]
                ]
            ]
        );

        return $this->chart;
    }
} 