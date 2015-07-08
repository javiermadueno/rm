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
use Symfony\Component\Translation\TranslatorInterface;


class PorcentajeDeVentas
{
    /**
     * @var Highchart
     */
    private $chart;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function getGraficoPorcentajeVentas(ResultadoMensual $mes, $nombre)
    {


        $this->chart = new Highchart();
        $this->chart->chart->renderTo($nombre);

        $this->chart->plotOptions->pie ( [
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => false
                ],
                'showInLegend' => true
            ] );

        if (!$mes) {
            return $this->chart;
        }

        $this->chart->title->text ( $this->translator->trans(
            'highchart.insight.clientes.porcentaje.ventas.title',
            ['%mes%' =>  $mes->getFecha()->format('F-Y')]
        ));

        $this->chart->series ( [
                [
                    'type' => 'pie',
                    'name' => $this->translator->trans('highchart.insight.clientes.porcentaje.ventas.ventas'),
                    'data' => [
                        [$this->translator->trans('highchart.insight.clientes.porcentaje.ventas.miembros'),    $mes->getVentasCliente()  ? $mes->getVentasCliente()   : 0.00 ],
                        [$this->translator->trans('highchart.insight.clientes.porcentaje.ventas.no.miembros'), $mes->getVentasNoCliente()? $mes->getVentasNoCliente() : 0.00 ]
                    ]
                ]
            ]
        );

        return $this->chart;
    }
} 