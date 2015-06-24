<?php

namespace RM\LinealesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use RM\DiscretasBundle\Entity\Tipo;

class VilRepository extends EntityRepository
{
	public function obtenerVariablesLineales($nombre = '', $tipoVar = 0) {

		
		$dql = "select l
			from RMLinealesBundle:Vil l
			where l.estado = 1";
		if($nombre != ''){
			$dql .= " AND l.nombre LIKE :nombre";
		}
		if($tipoVar > 0){
			$dql .= " AND l.tipo = :tipo";
		}
		$dql .= " ORDER BY l.nombre";
	
			
		$query = $this->_em->createQuery($dql);
		if($nombre != ''){
			$query->setParameter('nombre', '%' . $nombre . '%');
		}
		if($tipoVar > 0){
			$query->setParameter('tipo', $tipoVar);
		}

		
		$variables = $query->getResult();
		
		return $variables;
		
	}
	
	public function obtenerVLbyId($id_vil) {

	
		$dql = "select l
			from RMLinealesBundle:Vil l
			where l.estado = 1
			AND l.idVil = :idVil";
			
		$query = $this->_em->createQuery($dql);
		$query->setParameter('idVil', $id_vil);
	
		$registros = $query->getResult();
	
		return $registros;
	
	}

    public function obetenerVariablesLinealesNoSociodemograficas()
    {
        $dql = "SELECT l
                FROM RMLinealesBundle:Vil l
                JOIN RMDiscretasBundle:Tipo tipo WITH (l.tipo = tipo.id AND tipo.codigo != :codigo_sd )
                WHERE l.estado = 1";

        $query = $this->_em->createQuery($dql);

        $query->setParameter('codigo_sd', Tipo::SOCIODEMOGRAFICO);

        return $query->getResult();
    }

    public function findVariablesLinealesByTipo(Tipo $tipo)
    {
        $dql = "
            SELECT l
            FROM RMLinealesBundle:Vil l
            WHERE l.estado > -1
            AND l.tipo = :tipo
        ";

        return $this->_em->createQuery($dql)
            ->setParameter('tipo', $tipo->getId() )
            ->getResult();
    }

}