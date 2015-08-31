<?php

namespace RM\PlantillaBundle\DependencyInjection;
use RM\AppBundle\DependencyInjection\DoctrineManager;


class TamanyoImagenServicio
{
	public function __construct(DoctrineManager $manager)
	{
		$this->em = $manager->getManager();
	}
	
	public function getTIById($id_tamanyo){
		$repo      = $this->em->getRepository('RMPlantillaBundle:TamanyoImagen');
		$registros = $repo->obtenerTIById($id_tamanyo);
		return $registros;
	}
	
	public function getTIByTipo($tipo){
		$repo      = $this->em->getRepository('RMPlantillaBundle:TamanyoImagen');
		$registros = $repo->obtenerTIByTipo($tipo);
		return $registros;
	}
	
	public function getTIConInfoAsocByTipo($tipo){
		$repo      = $this->em->getRepository('RMPlantillaBundle:TamanyoImagen');
		$registros = $repo->obtenerTIConInfoAsocByTipo($tipo);
		return $registros;
	}
	
	public function eliminarTamanyosById($id_tamanyo){
		
		$objTams = $this->getTIById($id_tamanyo);
		foreach ($objTams as $objTam){
			$objTam->setEstado(-1);
			$this->em->persist($objTam);
		}
		$this->em->flush();
		
		return 1;
	}
}