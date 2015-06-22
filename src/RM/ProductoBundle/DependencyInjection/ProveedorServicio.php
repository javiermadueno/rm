<?php

namespace RM\ProductoBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;


class ProveedorServicio
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function getProveedorById($id_proveedor)
    {
        $registro = $this->em->getRepository('RMProductoBundle:Proveedor')->obtenerProveedorById($id_proveedor);
        return $registro;

    }

    public function getProveedores()
    {
        $registros = $this->em->getRepository('RMProductoBundle:Proveedor')->findAll();
        return $registros;

    }

    public function findProvedoresByNombre($nombre)
    {
        $res = $this->em->getRepository('RMProductoBundle:Proveedor')
            ->createQueryBuilder('p')
            ->where('p.nombre LIKE :nombre')
            ->setParameter('nombre', sprintf('%%%s%%', $nombre))
            ->getQuery()
            ->getResult();

        return $res;
    }
}