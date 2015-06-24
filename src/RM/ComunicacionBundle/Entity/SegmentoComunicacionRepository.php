<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SegmentoComunicacionRepository extends EntityRepository
{
	public function obtenerSegmentosComunicacion()
    {

		$dql = "select sc
			from RMComunicacionBundle:SegmentoComunicacion sc
			WHERE sc.estado > -1";
			
		$query = $this->_em->createQuery($dql);
	
		$registros = $query->getResult();
	
		return $registros;
	
	}
	
	public function obtenerSegmentosComunicacionById($id_comunicacion, $id_segmento, $estado = -1)
    {
        $qb = $this->createQueryBuilder('sc')
            ->where('sc.estado > -1')
        ;

		if($id_comunicacion != -1){
            $qb->andWhere('sc.idComunicacion = :comunicacion')
                ->setParameter('comunicacion', $id_comunicacion);
		}
		
		if($id_segmento != -1){
			$qb->andWhere('sc.idSegmento = :segmento')
                ->setParameter('segmento', $id_segmento);
		}

        $registros = $qb->getQuery()->getResult();

		return $registros;
	
	}
	
	public function obtenerSegmentosComunicacionBySC($id_segmento_comunicacion)
    {

		$dql = "select sc
			from RMComunicacionBundle:SegmentoComunicacion sc
			WHERE sc.idSegmentoComunicacion IN (:segmento_comunicacion)";
			
		$query = $this->_em
            ->createQuery($dql)
            ->setParameter('segmento_comunicacion', $id_segmento_comunicacion)
        ;
	
		$registros = $query->getResult();
	
		return $registros;
	
	}
	
	
	public function obtenerNuevosSegmentosParaComunicacion($id_comunicacion, $tipo = 5)
    {
	

		$dql = "select s
			from RMSegmentoBundle:Segmento s
			LEFT JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (sc.idSegmento = s.idSegmento AND sc.estado > -1 AND sc.idComunicacion =  :comunicacion)
			WHERE s.tipo IN ( :tipo )
			AND s.estado > -1
			AND sc.idSegmento IS NULL";
			
		$query = $this->_em
            ->createQuery($dql)
            ->setParameter('tipo', $tipo)
            ->setParameter('comunicacion', $id_comunicacion)
        ;
	
		$registros = $query->getResult();
	
		return $registros;
	
	}

    public function findFechaProximaEjecucionByComunicacion(Comunicacion $comunicacion)
    {
        $dql = "
            SELECT MIN(sc.proximaEjecucion)
            FROM   RMComunicacionBundle:SegmentoComunicacion sc
            WHERE sc.idComunicacion = :id_comunicacion
            AND sc.estado != :estado_completada
            AND sc.estado > -1
            AND sc.proximaEjecucion > :now
        ";

        return $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_comunicacion', $comunicacion->getIdComunicacion())
            ->setParameter('estado_completada',Comunicacion::ESTADO_COMPLETADA)
            ->setParameter('now', new \DateTime())
            ->getSingleScalarResult();
    }

    public function findSegmentosComunicacionByComunicacion(Comunicacion $comunicacion)
    {
        $dql = "
            SELECT sc
            FROM RMComunicacionBundle:SegmentoComunicacion sc
            WHERE sc.idComunicacion = :idComunicacion
            AND sc.estado > -1
        ";

        return $query = $this->_em
            ->createQuery($dql)
            ->setParameter('idComunicacion', $comunicacion->getIdComunicacion())
            ->getResult();
    }
}