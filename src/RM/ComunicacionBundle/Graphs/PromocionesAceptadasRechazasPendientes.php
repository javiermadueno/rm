<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 07/09/2015
 * Time: 12:58
 */

namespace RM\ComunicacionBundle\Graphs;


use Ob\HighchartsBundle\Highcharts\Highchart;
use RM\ComunicacionBundle\Model\Abstracts\InstanciaComunicacionAbstract;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\Translation\TranslatorInterface;

class PromocionesAceptadasRechazasPendientes
{

    /** @var TranslatorInterface  */
    private  $translator;

    public function __construct(TranslatorInterface $traslator)
    {
        $this->translator = $traslator;
    }

    /**
     * @param InstanciaComunicacionAbstract $instancia
     *
     * @return Highchart
     */
    public function porcentajePromocionesPorEstado(InstanciaComunicacionAbstract $instancia)
    {
        $objGT = new Highchart ();
        $objGT->chart->renderTo('graficoTarta');
        $objGT->title->text($this->translator->trans('highchart.intancia.cierre.porcentaje.promociones.elaboradas'));
        $objGT->plotOptions->pie(
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
        $objGT->tooltip->pointFormat(
            '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
        )
        ;

        $total = $instancia->getPromocionesSegmentadas()->count();
        $data = [
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.pendientes'),
                'color' => '#e67e22',
                'y'     => $total != 0 ? round((($instancia->getTotalPromocionesPendientes()) / $total ) * 100, 2) : 0
            ],
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.aceptadas'),
                'color' => '#2ecc71',
                'y'     => $total != 0 ? round(($instancia->getTotalPromocionesAceptadas() / $total) * 100, 2) : 0
            ],
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.rechazadas'),
                'color' => '#e74c3c',
                'y'     => $total != 0 ? round((($instancia->getTotalPromocionesRechazadas()) / $total) * 100, 2) : 0
            ]
        ];
        $objGT->series(
            [
                [
                    'type' => 'pie',
                    'name' => $this->translator->trans('promociones'),
                    'data' => $data
                ]
            ]
        )
        ;

        return $objGT;
    }

    public function promocionesAceptadasRechazadasPendientesPorGrupoSlot(InstanciaComunicacionAbstract $instancia)
    {
        $aceptadas  = [];
        $pendientes = [];
        $rechazadas = [];

        foreach ($instancia->getGruposSlots() as $grupo) {
            $num_aceptadas  = 0;
            $num_pendientes = 0;
            $num_rechazadas = 0;

            $nombreGrupos[] = $grupo->getNombre();

            /** @var NumPromociones $numPro */
            foreach ($instancia->getNumPromocionesByGrupo($grupo) as $numPro) {
                $num_aceptadas   += $numPro->getTotalPromocionesAceptadas();
                $num_rechazadas  += $numPro->getTotalPromocionesRechazadas();
                $num_pendientes  += $numPro->getTotalPromocionesPendientes();
            }

            $aceptadas[]  = $num_aceptadas;
            $rechazadas[] = $num_rechazadas;
            $pendientes[] = $num_pendientes;

        }




        $series = [
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.pendientes'),
                'type'  => 'column',
                'color' => '#e67e22',
                'data'  => $pendientes
            ],
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.aceptadas'),
                'type'  => 'column',
                'color' => '#2ecc71',
                'data'  => $aceptadas
            ],
            [
                'name'  => $this->translator->trans('highchart.intancia.cierre.rechazadas'),
                'type'  => 'column',
                'color' => '#e74c3c',
                'data'  => $rechazadas
            ]
        ];



        $objGB = new Highchart ();
        $objGB->chart->renderTo('graficoBarras'); // The #id of the div where to render the chart
        $objGB->chart->type('column');
        $objGB->title->text($this->translator->trans('highchart.intancia.cierre.promociones.aceptadas.rechazadas'));
        $objGB->xAxis->categories($nombreGrupos);
        $objGB->yAxis->min('0');
        $objGB->yAxis->title(
            [
                'text' => $this->translator->trans('promociones')
            ]
        )
        ;
        $objGB->legend->enabled(true);

        $objGB->series($series);

        return $objGB;
    }

} 