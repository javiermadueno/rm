<?php

namespace RM\ProductoBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProductoBundle\Entity\Promocion;
use \NumberFormatter;

class PromocionServicio
{

	public function __construct(DoctrineManager $doctrine) {
		$this->em = $doctrine->getManager();
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