<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ComunicacionRepository extends EntityRepository
{
	public function obtenerComunicaciones($id_canal, $estado)
    {
		$dql = "select c
			from RMComunicacionBundle:Comunicacion c
			WHERE 1=1";
		
		if($id_canal != -1){
			$dql .= " AND c.idCanal = :id_canal";
		}
		
		if($estado != -2){
			$dql .= " AND c.estado = :estado";
		}
		else {
			$dql .= " AND c.estado > -1";
		}

		$query = $this->_em->createQuery($dql);
		if($id_canal != -1){
			$query->setParameter('id_canal', $id_canal);
		}
		if($estado != -2){
			$query->setParameter('estado', $estado);
		}
		$registros = $query->getResult();
	
		return $registros;
	
	}
	
	public function deleteComunicaciones($id_comunicacion) {

		$dql = "
				UPDATE RMComunicacionBundle:Comunicacion c
				SET c.estado = -1
				WHERE c.idComunicacion = :idComunicacion
				";
		
		$query = $this->_em
            ->createQuery($dql)
            ->setParameter('idComunicacion', $id_comunicacion)
        ;
		
		$registros = $query->getResult();
	
		return $registros;
	
	}
	
	public function obtenerComunicacionById($id_comunicacion)
    {

		$dql = "
            select c
            from RMComunicacionBundle:Comunicacion c
            WHERE c.idComunicacion = :idComunicacion
        ";
	
		$query = $this->_em
            ->createQuery($dql)
            ->setParameter('idComunicacion', $id_comunicacion);

		$registros = $query->getResult();
	
		return $registros;
	
	}

    public function findById($id)
    {
        return $this->createQueryBuilder('c')
            ->join('c.plantilla', 'plantilla')->addSelect('plantilla')
            ->join('c.idCanal', 'canal')
            ->where('c.idComunicacion = :id')
            ->andWhere('c.estado > -1')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    public function findByPlantilla($plantilla)
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado > -1')
            ->andWhere('c.plantilla = :plantilla')
            ->andWhere('c.generada = 1')
            ->setParameter('plantilla', $plantilla)
            ->getQuery()->getResult();
    }

	
}