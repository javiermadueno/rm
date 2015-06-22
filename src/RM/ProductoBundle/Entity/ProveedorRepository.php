<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProveedorRepository extends EntityRepository
{
    public function obtenerProveedores()
    {

        //$em = $this->getEntityManager();
        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "select p
		from RMProductoBundle:Proveedor p";

        $query = $em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerProveedorById($id_proveedor)
    {

        //$em = $this->getEntityManager();
        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "select p
		from RMProductoBundle:Proveedor p
		WHERE p.idProveedor = :id_proveedor";

        $query = $em->createQuery($dql);
        $query->setParameter('id_proveedor', $id_proveedor);
        $registro = $query->getResult();

        return $registro;
    }
}
