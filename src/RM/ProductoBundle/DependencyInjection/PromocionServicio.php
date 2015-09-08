<?php

namespace RM\ProductoBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\Config\Definition\Exception\Exception;
use \NumberFormatter;

class PromocionServicio {

	public function __construct(DoctrineManager $doctrine) {
		$this->em = $doctrine->getManager();
	}


	public function getPromocionById($id_promocion) {
		$repo = $this->em->getRepository ( 'RMProductoBundle:Promocion' );
		$registros = $repo->obtenerPromocionById ( $id_promocion );
		return $registros;
	}

	public function getPromocionesByIdInstancia($id_instancia){
		
		$repo = $this->em->getRepository ( 'RMProductoBundle:Promocion' );
		$registros = $repo->obtenerPromocionesByIdInstancia( $id_instancia );
		return $registros;
	}

	public function getPromocionesCampanya($id_categoria, $id_instancia, $tipo) {
		$repo = $this->em->getRepository ( 'RMProductoBundle:Promocion' );
		$registros = $repo->obtenerPromocionesCampanya ( $id_categoria, $id_instancia, $tipo );
		return $registros;
	}
	public function getPromocionAsignadaSlot($id_slot, $id_plantilla) {
		$repo = $this->em->getRepository ( 'RMProductoBundle:Promocion' );
		$registros = $repo->obtenerPromocionAsignadaSlot ( $id_slot, $id_plantilla );
		return $registros;
	}


	public function actualizarPromocionesCampanya($data) {
		
		$repo = $this->em->getRepository ( 'RMProductoBundle:Promocion' );
		$val =0;
		
		foreach ( $data as $promoArray ) {
			if ($promoArray != NULL) {
		
				$respuesta = $repo->actualizarPromocionesCampanya ( $promoArray );
				$val++;
			}
		}
		
		if($respuesta == 1){
			return 1;
		}
		else{
			return -1;
		}
		
		
	}
}