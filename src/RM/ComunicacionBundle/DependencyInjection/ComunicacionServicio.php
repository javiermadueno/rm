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


    /**
     * @param Comunicacion $objComunicacion
     * @return bool|Comunicacion
     */
	public function guardarComunicaciones(Comunicacion $objComunicacion)
    {
		try {
            $this->em->persist($objComunicacion);
            $this->em->flush();

        } catch(\Exception $e) {
            return false;
        }

        return $objComunicacion;
	}

    /**
     * @param $id_comunicacion
     * @return bool|Comunicacion
     */
    public function pararComunicacion($id_comunicacion)
    {
        $comunicacion = $this->em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->find($id_comunicacion);

        if(!$comunicacion instanceof Comunicacion) {
            return false;
        }

        $comunicacion->setEstado(Comunicacion::ESTADO_PAUSADO);

        return $this->guardarComunicaciones($comunicacion);
    }

    /**
     * @param $id_comunicacion
     * @return bool|Comunicacion
     */
    public function reanudarComunicacion($id_comunicacion)
    {
        $comunicacion = $this->em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->find($id_comunicacion);

        if(!$comunicacion instanceof Comunicacion) {
            return false;
        }

        $comunicacion->setEstado(Comunicacion::ESTADO_ACTIVO);

        $comunicacion->check();

        $comunicacion->proximaEjecucion();

        return $this->guardarComunicaciones($comunicacion);
    }

    /**
     * @param Comunicacion $comunicacion
     * @return mixed
     */
    public function calculaFechaProximaEjecucion(Comunicacion $comunicacion)
    {
        if(!$comunicacion) {
            return false;
        }

        /**@var string $fechaEjecucion */
        $fechaEjecucion = $this->em
            ->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->findFechaProximaEjecucionByComunicacion($comunicacion);

        if(is_null($fechaEjecucion)) {
            return null;
        }

        return new \DateTime($fechaEjecucion);
    }
}