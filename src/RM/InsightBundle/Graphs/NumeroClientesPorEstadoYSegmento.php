<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/05/2015
 * Time: 14:17
 */

namespace RM\InsightBundle\Graphs;


use Ob\HighchartsBundle\Highcharts\Highchart;
use Zend\Json\Expr;

class NumeroClientesPorEstadoYSegmento
{

    public function graficaPorSexo($datos, $nombre = '')
    {
        if (empty($nombre)) {
            throw new \Exception('Es necesario proporcionar un nombre a la grafica');
        }

        if (!$datos) {
            return null;
        }

        /** @var \DateTime $fecha */
        $fecha = $datos['fecha'];

        $chart = new Highchart();

        $chart->chart
            ->plotBackgroundColor(null)
            ->plotBorderWidth(null)
            ->plotShadow(false)
            ->renderTo($nombre);

        $chart->title
            ->text('Clientes nuevos por Sexo - ' . $fecha->format('M-Y'));

        $chart->tooltip
            ->pointFormat(new Expr("'{series.name}: <b>{point.percentage:.1f}%</b>'"));

        $chart->plotOptions
            ->pie([
                'allowPointSelect' => true,
                'cursor'           => 'pointer',
                'dataLabels'       => [
                    'enabled' => true
                ],
                'showInLegend'     => true
            ]);

        $chart->series([
            [
                'type' => 'pie',
                'name' => sprintf('Clientes nuevos por Sexo - %s', $fecha->format('M-Y')),
                'data' => $datos['data']
            ]
        ]);

        return $chart;
    }

    public function graficoPorEdades($datos, $nombre)
    {
        $chart = new Highchart();

        $chart->chart
            ->renderTo('graficoBarrasEdad')
            ->type('column');

        $chart->title->text('Clientes Nuevos por Edades');

        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => 'Clientes Nuevos'
                ]
            ]
        ]);

        $chart->tooltip
            ->headerFormat('<span style="font-size:10px">{point.key}</span><table>')
            ->pointFormat('<tr><td style="color:{series.color};padding:0">{series.name}: </td>' . '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>')
            ->footerFormat('</table>')
            ->shared(true)
            ->useHTML(true);

        $chart->plotOptions->column([
            'pointPadding' => 0.2,
            'borderWidth'  => 0
        ]);

        $chart->xAxis->categories([
            'niños',
            'adolescentes',
            'jovenes',
            'jovenes adultos',
            'adultos',
            'maduros',
            'jubilados'
        ]);

        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => 'Clientes nuevos'
                ]
            ]
        ]);

    }

} 