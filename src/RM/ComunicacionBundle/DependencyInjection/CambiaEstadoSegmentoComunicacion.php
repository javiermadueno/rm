<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 10/02/2015
 * Time: 17:26
 */

namespace RM\ComunicacionBundle\DependencyInjection;


use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;

class CambiaEstadoSegmentoComunicacion
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }


    /**
     * Marca el segmentoDComunicacion como PAUSADO
     *
     * @param $idSegmentoComunicacion
     *
     * @return bool
     */
    public function pararSegmentoComunicacion($idSegmentoComunicacion)
    {
        $segmentoComunicacion =
            $this->getSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return false;
        }

        $segmentoComunicacion
            ->setEstado(Comunicacion::ESTADO_PAUSADO);

        return $this->guardarSegmentoComunicacion($segmentoComunicacion);
    }

    /**
     * @param int $idSegmentoComunicacion
     *
     * @return SegmentoComunicacion
     */
    private function getSegmentoComunicacion($idSegmentoComunicacion = 0)
    {
        return $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->find($idSegmentoComunicacion);
    }

    /**
     * @param SegmentoComunicacion $segmentoComunicacion
     *
     * @return null|SegmentoComunicacion
     */
    private function guardarSegmentoComunicacion(SegmentoComunicacion $segmentoComunicacion)
    {
        try {
            $this->em->persist($segmentoComunicacion);
            $this->em->flush();
        } catch (\Excepcion $e) {
            return null;
        }

        return $segmentoComunicacion;
    }

    /**
     * Marca el segmento comunicacion como ACTIVO
     *
     * @param $idSegmentoComunicacion
     *
     * @return bool
     */
    public function reanudarSegmentoComunicacion($idSegmentoComunicacion)
    {
        $segmentoComunicacion =
            $this->getSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return false;
        }

        $segmentoComunicacion
            ->setEstado(Comunicacion::ESTADO_ACTIVO);

        return
            $this->guardarSegmentoComunicacion($segmentoComunicacion);
    }

    /**
     * Elimina el segmentoComunicacion
     *
     * @param $idSegmentoComunicacion
     *
     * @return bool
     */
    public function eliminarSegmentoComunicacion($idSegmentoComunicacion)
    {
        $segmentoComunicacion =
            $this->getSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return false;
        }

        $segmentoComunicacion
            ->setEstado(-1);

        return
            $this->guardarSegmentoComunicacion($segmentoComunicacion);
    }
} 