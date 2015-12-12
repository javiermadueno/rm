<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/06/2015
 * Time: 12:18
 */

namespace RM\InsightBundle\Graphs;

use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Translation\TranslatorInterface;
use Zend\Json\Expr;

class BaseGraph
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

    }

    /**
     * @return Highchart
     */
    protected  function graficoColumnas()
    {
        $chart =  new Highchart();

        $chart->chart
            ->type('column')
        ;

        $chart->title->text('Sexo de Clientes');

        $chart->yAxis([[
            'min' => 0,
            'title' => [
                'text' => 'Número de clientes'
            ]
        ]]);

        $chart->tooltip
            ->headerFormat('<span style="font-size:10px">{point.key}</span><table>')
            ->pointFormat('<tr><td style="color:{series.color};padding:0">{series.name}: </td>'.'<td style="padding:0"><b>{point.y}</b></td></tr>')
            ->footerFormat('</table>')
            ->shared(true)
            ->useHTML(true)
        ;

        $chart->plotOptions->column([
            'pointPadding' => 0.2,
            'borderWidth'  => 0
        ]);

        return $chart;
    }

    /**
     * @param $renderTo
     *
     * @return Highchart|null
     */
    public function graphPieNodata($renderTo)
    {
        if (!$renderTo) {
            return;
        }

        $chart = $this->graphNoData();
        $chart->chart
            ->renderTo($renderTo)
            ->type('pie');

        $chart->series([[
            'type' => 'pie',
            'name' => $this->translator->trans('sin.resultados'),
            'data' => []
        ]]);

        return $chart;

    }

    /**
     * @param $renderTo
     *
     * @return Highchart
     */
    public function graphColumnNoData($renderTo)
    {
        if (!$renderTo) {
            return;
        }

        $chart = $this->graphNoData();
        $chart->chart
            ->renderTo($renderTo)
            ->type('column');

        $chart->series([[
            'type' => 'column',
            'name' => $this->translator->trans('sin.resultados'),
            'data' => []
        ]]);

        return $chart;
    }

    /**
     * @return Highchart
     */
    protected  function graphNoData()
    {
        $chart = new Highchart();

        $chart->chart
            ->plotBackgroundColor(null)
            ->plotBorderWidth(null)
            ->plotShadow(null);

        $chart->title->text($this->translator->trans('sin.resultados'));

        return $chart;
    }

    protected function graficoStackColumnas()
    {
        $chart = $this->graficoColumnas();

        $chart->plotOptions->column([
            'stacking' => 'normal',
            'dataLabels' => [
                'enabled' => true,
                'color' => new Expr("(Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'"),
                'style' => [
                    'textShadow' => '0 0 3px black'
                ]
            ]
        ]);

        $chart->yAxis([[
            'min' => 0,
            'title' => [
                'text' => $this->translator->trans('numero.clientes')
            ],
            'stackLabels' => [
                'enabled' => false,
                'style' => [
                    'fontWeight' => 'bold',
                    'color' => new Expr("(Highcharts.theme && Highcharts.theme.textColor) || 'gray'")
                ]
            ]
        ]]);

        return $chart;
    }

    protected function prepareData($data = [], $categories)
    {
        $categorias = [];
        $resultado  = [];
        $index = 0;

        $categories = $this->sanitize($categories);

        foreach ($categories as $category) {

            $points = [];
            foreach($data as $series) {
                $points[] = $series['data'][$index];
                if($index === 0) $categorias[] = $series['name'];
            }

            $res = [
                'name' => $category,
                'data' => $points
            ];

            $resultado[] = $res;
            $index++;
        }

        return [
            'categorias' => array_unique($categorias),
            'series' => $resultado
        ];

    }

    protected  function sanitize($nombres = [], $index = 1)
    {
        return array_map(function($item) use ($index) {
            return ucfirst(mb_strtolower(explode('_', $item)[$index],'UTF-8'));
        }, $nombres);
    }
}