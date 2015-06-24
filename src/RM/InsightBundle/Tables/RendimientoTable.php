<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 10/06/2015
 * Time: 10:30
 */

namespace RM\InsightBundle\Tables;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\RMMongoBundle\DependencyInjection\EstadisticasClientes;
use RM\RMMongoBundle\Util;

class RendimientoTable
{

    public function __construct(EstadisticasClientes $repository, DoctrineManager $manager)
    {
        $this->repository = $repository;
        $this->em = $manager->getManager();
    }

    /**
     * @param array $meses
     * @param array $estructura_segmentos
     *
     * @return array
     */
    public function tablaRendimiento($meses = [], $estructura_segmentos = [])
    {
        $nombre_segmentos = Util::array_flatten($estructura_segmentos, []);

        $ids_segmentos = $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre($nombre_segmentos);

        $resultado = [];


        foreach ($estructura_segmentos as $segmento1 => $segmentos) {
            foreach ($segmentos as $segmento2 => $val) {
                $ids = [$ids_segmentos[$segmento1], $ids_segmentos[$segmento2]];
                $resultado[$segmento1][$segmento2] = $this->repository
                    ->findEstadisticasClientesByMesYSegmento($meses, $ids);
            }
        }

        return $resultado;
    }


}