<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/09/2015
 * Time: 13:04
 */

namespace RM\ComunicacionBundle\Graphs;

use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\Translation\TranslatorInterface;

class PromocionesRealizadasVsTotales
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return Highchart
     */
    public function createPie(InstanciaComunicacion $instancia, $renderTo = '')
    {

        $data = $this->calculaNumeroPromocionesRealizadasYTotales($instancia);

        $grafica = new Highchart ();
        $grafica->chart->renderTo($renderTo);
        $grafica->title->text($this->trans('highchart.intancia.negociacion.porcentaje.promociones.elaboradas'));
        $grafica->plotOptions->pie(
            [
                'allowPointSelect' => true,
                'cursor'           => 'pointer',
                'dataLabels'       => [
                    'enabled' => false,
                    'format'  => '<b>{point.name}</b>: {point.percentage:.1f} %',
                ],
                'showInLegend'     => true
            ]
        )
        ;
        $grafica->tooltip->pointFormat(
            '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
        )
        ;

        $grafica->series(
            [
                [
                    'type' => 'pie',
                    'name' => $this->trans('promociones'),
                    'data' => $data
                ]
            ]
        )
        ;

        return $grafica;

    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return mixed
     */
    public function calculaNumeroPromocionesRealizadasYTotales(InstanciaComunicacion $instancia)
    {
        $res = array_reduce(
            $instancia->getNumPromociones()->toArray(),
            function ($estadisticas = [], NumPromociones $numPromociones) {
                isset($estadisticas['realizadas']) ?: $estadisticas['realizadas'] = 0;
                isset($estadisticas['total']) ?: $estadisticas['total'] = 0;

                $estadisticas['realizadas'] += $numPromociones->getTotalPromocionesRealizadas();
                $estadisticas['total'] += $numPromociones->getTotalPromociones();

                return $estadisticas;
            }
        );

        $data = [
            [
                $this->trans('highchart.intancia.negociacion.realizadas'),
                $res['total'] != 0 ? round(($res['realizadas'] / $res['total']) * 100, 2) : 0
            ],
            [
                $this->trans('highchart.intancia.negociacion.restantes'),
                $res['total'] != 0 ? round((($res['total'] - $res['realizadas']) / $res['total']) * 100, 2) : 0
            ]
        ];

        return $data;
    }

    /**
     * @param string $mensaje
     *
     * @return string
     */
    private function trans($mensaje = '')
    {
        return $this->translator->trans($mensaje);
    }

    /**
     * @param InstanciaComunicacion $instancia
     * @param string                $renderTo
     *
     * @return Highchart
     */
    public function createBar(InstanciaComunicacion $instancia, $renderTo = '')
    {
        $data = $this->calcula($instancia);

        $objGB = new Highchart ();
        $objGB->chart->renderTo('graficoBarras');
        $objGB->chart->type('column');
        $objGB->title->text($this->trans('highchart.intancia.negociacion.promociones.elaboradas.requeridas'));
        $objGB->xAxis->categories($data['categories']);
        $objGB->yAxis->min('0');
        $objGB->yAxis->title(
            [
                'text' => $this->trans('promociones')
            ]
        )
        ;
        $objGB->legend->enabled(true);

        $objGB->series($data['series']);

        return $objGB;

    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return array
     */
    public function calcula(InstanciaComunicacion $instancia)
    {
        $grupos = $instancia->getGruposSlots()->toArray();

        $data = [];

        /** @var GrupoSlots $grupo */
        foreach ($grupos as $grupo) {

            $total      = [];
            $realizadas = [];
            /** @var Numpromociones $numPromocion */
            foreach ($instancia->getNumPromocionesByGrupo($grupo) as $numPromocion) {
                $total[]      = $numPromocion->getTotalPromociones();
                $realizadas[] = $numPromocion->getTotalPromocionesRealizadas();
            }

            $data[$grupo->getNombre()] = [
                'total'      => array_sum($total),
                'realizadas' => array_sum($realizadas)
            ];
        }

        $series = [
            [
                'name'  => $this->trans('highchart.intancia.negociacion.realizadas'),
                'type'  => 'column',
                'color' => '#4572A7',
                'data'  => array_column($data, 'realizadas')
            ],
            [
                'name'  => $this->trans('highchart.intancia.negociacion.totales'),
                'type'  => 'column',
                'color' => '#AA4643',
                'data'  => array_column($data, 'total')
            ]
        ];

        $categories = array_keys($data);

        return ['series' => $series, 'categories' => $categories];


    }

} 