<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProveedorRepository extends EntityRepository
{
	public function obtenerProveedores() {

		$dql = "select p
		from RMProductoBundle:Proveedor p";
			
		$query = $this->_em->createQuery($dql);
	
		$registros = $query->getResult();
	
		return $registros;
	}
	
	public function obtenerProveedorById($id_proveedor) {
	

	
		$dql = "select p
		from RMProductoBundle:Proveedor p
		WHERE p.idProveedor = :id_proveedor";
			
		$query = $this->_em->createQuery($dql);
		$query->setParameter('id_proveedor', $id_proveedor);
		$registro = $query->getResult();
	
		return $registro;
	}
}
