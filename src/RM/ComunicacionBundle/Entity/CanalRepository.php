<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CanalRepository extends EntityRepository
{

	public function obtenerCanales()
    {

		$dql = "
            select c
			  from RMComunicacionBundle:Canal c
        ";
			
		$query = $this->_em->createQuery($dql);
	
		$registros = $query->getResult();
	
		return $registros;
	
	}
	
	public function obtenerCanalById($id_canal)
    {
		$dql = "
          select c
			from RMComunicacionBundle:Canal c
           WHERE c.idCanal = :idcanal
        ";
			
		$query = $this->_em->createQuery($dql);
		$query->setParameter('idcanal', $id_canal);
	
		$registros = $query->getResult();
	
		return $registros;
	
	}
}