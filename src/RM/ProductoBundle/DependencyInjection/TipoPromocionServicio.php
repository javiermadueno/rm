<?php

namespace RM\ProductoBundle\DependencyInjection;
use RM\AppBundle\DependencyInjection\DoctrineManager;


class TipoPromocionServicio
{
	public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
    }
    
    public function getTipos()
    {
    	$registros = $this->em->getRepository('RMProductoBundle:TipoPromocion')->obtenerTipos();
    	return $registros;
    
    }
}