<?php

namespace RM\ProductoBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;


class ProductoServicio
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }


    public function getProductosByMarca($id_marca)
    {
        $repo = $this->em->getRepository('RMProductoBundle:Producto');
        return $repo->obtenerProductosByMarca($id_marca);
    }

    public function getProductosByCodigo($codigo)
    {
        $repo = $this->em->getRepository('RMProductoBundle:Producto');
        return $repo->obtenerProductosByCodigo($codigo);
    }


}