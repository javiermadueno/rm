<?php

namespace RM\ProductoBundle\DependencyInjection;
use RM\AppBundle\DependencyInjection\DoctrineManager;


class ProductoServicio
{
	public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
    }


    public function getProductosByMarca($id_marca){
        $repo = $this->em->getRepository('RMProductoBundle:Producto');
        return $repo->obtenerProductosByMarca($id_marca);
    }

    public function getProductosByCodigo($codigo)
    {
        $repo = $this->em->getRepository('RMProductoBundle:Producto');
        return $repo->obtenerProductosByCodigo($codigo);
    }


    
}