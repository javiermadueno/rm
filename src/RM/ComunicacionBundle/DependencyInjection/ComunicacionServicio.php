<?php

namespace RM\ComunicacionBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ComunicacionBundle\Entity\Comunicacion;

class ComunicacionServicio
{
    public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
    }

    public function getComunicaciones($id_canal = -1, $estado = -2)
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Comunicacion');
        $registros = $repo->obtenerComunicaciones($id_canal, $estado);
        return $registros;
    }

    public function getComunicacionById($id_comunicacion)
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Comunicacion');
        $registros = $repo->obtenerComunicacionById($id_comunicacion);
        return $registros;
    }

    public function deleteComunicaciones($id_comunicacion)
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Comunicacion');
        $registros = $repo->deleteComunicaciones($id_comunicacion);
        $this->em->flush();
        return 1;
    }

    /**
     * @param Comunicacion $objComunicacion
     *
     * @return bool|Comunicacion
     */
    public function guardarComunicaciones(Comunicacion $objComunicacion)
    {
        try {
            $this->em->persist($objComunicacion);
            $this->em->flush();

        } catch (\Exception $e) {
            return false;
        }

        return $objComunicacion;
    }

    /**
     * @param $id_comunicacion
     *
     * @return bool|Comunicacion
     */
    public function pararComunicacion($id_comunicacion)
    {
        $comunicacion = $this->em->getRepository('RMComunicacionBundle:Comunicacion')
            ->find($id_comunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            return false;
        }

        $comunicacion->setEstado(Comunicacion::ESTADO_PAUSADO);

        return $this->guardarComunicaciones($comunicacion);
    }

    /**
     * @param $id_comunicacion
     *
     * @return bool|Comunicacion
     */
    public function reanudarComunicacion($id_comunicacion)
    {
        $comunicacion = $this->em->getRepository('RMComunicacionBundle:Comunicacion')
            ->find($id_comunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            return false;
        }

        $comunicacion->setEstado(Comunicacion::ESTADO_ACTIVO);

        $comunicacion->setFecProximaEjecucion($this->calculaFechaProximaEjecucion($comunicacion));

        return $this->guardarComunicaciones($comunicacion);
    }

    /**
     * @param Comunicacion $comunicacion
     *
     * @return mixed
     */
    public function calculaFechaProximaEjecucion(Comunicacion $comunicacion)
    {
        if (!$comunicacion) {
            return false;
        }

        /**@var string $fechaEjecucion */
        $fechaEjecucion = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->findFechaProximaEjecucionByComunicacion($comunicacion);

        if (is_null($fechaEjecucion)) {
            return null;
        }

        return new \DateTime($fechaEjecucion);
    }
}