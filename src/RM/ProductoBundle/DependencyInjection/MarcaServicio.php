<?php

namespace RM\ProductoBundle\DependencyInjection;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use RM\AppBundle\DependencyInjection\DoctrineManager;


class MarcaServicio
{
	public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
    }
    
    public function getMarcaById($id_marca)
    {
    	$registros = $this->em->getRepository('RMProductoBundle:Marca')->obtenerMarca($id_marca);
    	return $registros;
    
    }
    
    public function getMarcas()
    {
    	$registros = $this->em->getRepository('RMProductoBundle:Marca')->obtenerMarcas();
    	return $registros;
    }
    
    public function getMarcasByCategoria($id_categoria)
    {
    	$registros = $this->em->getRepository('RMProductoBundle:Marca')->obtenerMarcasByCategoria($id_categoria);
    	return $registros;
    }

    public function getMarcasByIdsCategoria($idsCategorias = []){
      return $this->em->getRepository('RMProductoBundle:Marca')->obtenerMarcasByIdsCategoria($idsCategorias);
    }
}