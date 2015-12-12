<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/10/2015
 * Time: 10:20
 */

namespace RM\InsightBundle\Graphs;


use Doctrine\ORM\EntityManager;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Translation\TranslatorInterface;
use Zend\Json\Expr;

class InsightCampanyasGraph extends BaseGraph
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em, TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->em = $em;
    }

    /**
     * @param array  $resultado
     * @param string $renderTo
     *
     * @return \Ob\HighchartsBundle\Highcharts\Highchart
     */
    public function graficaReactividadReactanciaVentas($resultado = [], $renderTo = "desglose")
    {
        if (!$resultado || empty($resultado)) {
            return $this->graphColumnNoData($renderTo);
        }

        $chart = $this->graficoColumnas();

        $reactividad = [];
        $redencion   = [];
        $ventas      = [];
        $meses       = [];

        setlocale(LC_ALL, 'es');

        foreach ($resultado as $instancia) {
            list($year, $month) = explode('-', $instancia['_id']);
            $mes = new \DateTime(sprintf('01-%s-%s', $month, $year));

            $meses[]       = strftime('%b %Y', $mes->getTimestamp());
            $reactividad[] = isset($instancia['reactividad']) ? round($instancia['reactividad'] * 100, 2) : 0.00;
            $redencion[]   = isset($instancia['redencion']) ? round($instancia['redencion'] * 100, 2) : 0.00;
            $ventas[]      = $instancia['total_ventas'];
        }


        $chart->title->text($this->translator->trans('highchart.insight.campaigns.reactividad.redencion.ventas.title'));
        $chart->chart->renderTo($renderTo);
        $chart->xAxis->categories($meses);
        $chart->yAxis([
            [
                'labels'   => [
                    'format' => '{value} %',
                    'style'  => [
                        'color' => new Expr('Highcharts.getOptions().colors[2]')
                    ]
                ],
                'opposite' => true,
                'min'      => 0
            ],
            [
                'gridLineWidth' => 0,
                'title'         => [
                    'text'  => $this->translator->trans('highchart.insight.campaigns.reactividad.redencion.ventas.serie.axis.y'),
                    'style' => [
                        'color' => new Expr('Highcharts.getOptions().colors[0]')
                    ]
                ],
                'format'        => '{value} Euros',
                'style'         => [
                    'color' => new Expr('Highcharts.getOptions().colors[0]')
                ]
            ],
        ])
        ;

        $chart->legend
            ->align('right')
            ->layout('vertical')
            ->verticalAlign('top')
            ->x(0)
            ->y(50)
        ;


        $series = [
            [
                'name'    => $this->translator->trans('highchart.insight.campaigns.reactividad.redencion.ventas.serie.axis.y'),
                'type'    => 'column',
                'yAxis'   => 1,
                'data'    => $ventas,
                'tooltip' => [
                    'valueSuffix' => ' Euros'
                ]
            ],
            [
                'name'      => $this->translator->trans('redencion'),
                'type'      => 'spline',
                'data'      => $redencion,
                'marker'    => [
                    'enabled' => false
                ],
                'dashStyle' => 'shortdot',
                'tooltip'   => [
                    'valueSuffix' => ' %'
                ]
            ],
            [
                'name'    => $this->translator->trans('reactividad'),
                'type'    => 'spline',
                'data'    => $reactividad,
                'tooltip' => [
                    'valueSuffix' => ' %'
                ]
            ]
        ];

        $chart->series($series);

        return $chart;

    }

    public function getGraficaDistribucionFidelidad($distribucion, $renderTo = '')
    {
        if (empty($distribucion)) {
            return $this->graphColumnNoData($renderTo);
        }

        $segmentos = $this
            ->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosFidelidad()
        ;

        $series = $this->getSeries($distribucion, $segmentos);

        $chart = $this->getStackedColumnPecent();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.campaigns.reactividad.target.fidelizacion.title'));
        $chart->xAxis->categories([$this->translator->trans('reactividad'), $this->translator->trans('target')]);
        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => $this->translator->trans('highchart.insight.campaigns.reactividad.target.fidelizacion.serie.axis.y')
                ]
            ]
        ])
        ;

        $chart->series($series);

        return $chart;
    }

    private function getSeries($distribucion, $segmentos)
    {
        if (empty($distribucion) || empty($segmentos)) {
            return [];
        }

        $series = [];
        foreach ($segmentos as $nombre => $id) {
            $index = array_search($id, array_column($distribucion, 'id'));
            if (false === $index) {
                continue;
            }

            $nombre = explode('_', $nombre)[1];

            $series[] = [
                'name' => ucfirst(mb_strtolower($nombre, 'UTF-8')),
                'data' => [$distribucion[$index]['reactividad'], $distribucion[$index]['target']]
            ];
        }

        return $series;
    }

    private function getStackedColumnPecent()
    {
        $chart = new Highchart();
        $chart->chart
            ->type('column')
        ;

        $chart->plotOptions->series([
            'stacking' => 'percent'
        ])
        ;

        $chart
            ->tooltip
            ->pointFormat('<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>')
            ->shared(true)
        ;

        $chart->legend
            ->align('right')
            ->layout('vertical')
            ->verticalAlign('top')
            ->x(0)
            ->y(50)
        ;


        return $chart;
    }

    public function getGraficaDistribucionSexos($distribucion, $renderTo = '')
    {
        if (empty($distribucion)) {
            return $this->graphColumnNoData($renderTo);
        }

        $segmentos = $this
            ->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosSexo()
        ;

        $series = $this->getSeries($distribucion, $segmentos);

        $chart = $this->getStackedColumnPecent();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.campaigns.reactividad.target.sexos.title'));
        $chart->xAxis->categories([$this->translator->trans('reactividad'), $this->translator->trans('target')]);
        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => $this->translator->trans('highchart.insight.campaigns.reactividad.target.sexos.serie.axis.y')
                ]
            ]
        ])
        ;

        $chart->series($series);

        return $chart;
    }

    public function  getGraficaDistribucionEdad($distribucion, $renderTo = '')
    {
        if (empty($distribucion)) {
            return $this->graphColumnNoData($renderTo);
        }

        $segmentos = $this
            ->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosEdad()
        ;

        $series = $this->getSeries($distribucion, $segmentos);

        $chart = $this->getStackedColumnPecent();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.campaigns.reactividad.target.edades.title'));
        $chart->xAxis->categories([$this->translator->trans('reactividad'), $this->translator->trans('target')]);
        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => $this->translator->trans('highchart.insight.campaigns.reactividad.target.edades.serie.axis.y')
                ]
            ]
        ])
        ;

        $chart->series($series);

        return $chart;
    }

    public function getGraficaDistribucionValor($distribucion, $renderTo = ''){
        if (empty($distribucion)) {
            return $this->graphColumnNoData($renderTo);
        }

        $segmentos = $this
            ->em
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosValor()
        ;

        $series = $this->getSeries($distribucion, $segmentos);

        $chart = $this->getStackedColumnPecent();
        $chart->chart->renderTo($renderTo);
        $chart->title->text($this->translator->trans('highchart.insight.campaigns.reactividad.target.tipo.cliente.title'));
        $chart->xAxis->categories([$this->translator->trans('reactividad'), $this->translator->trans('target')]);
        $chart->yAxis([
            [
                'min'   => 0,
                'title' => [
                    'text' => $this->translator->trans('highchart.insight.campaigns.reactividad.target.tipo.cliente.serie.axis.y')
                ]
            ]
        ])
        ;

        $chart->series($series);

        return $chart;
    }

} 