<?php

namespace RM\ProductoBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\Config\Definition\Exception\Exception;

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

    public function guardarPromocionesCampanya ($promociones)
    {

        $repo = $this->em->getRepository('RMProductoBundle:Promocion');
        $tipoPromocion = $this->em->getRepository('RMProductoBundle:TipoPromocion');
        try {
            foreach ($promociones as $idNumPro => $promocion) {

                $segmentadas = isset($promocion['segmentadas']) ? $promocion['segmentadas'] : [];

                foreach ($segmentadas as $segmentada) {

                    $producto = $this->em->find('RMProductoBundle:Producto', $segmentada['producto']);

                    if (!$producto) {
                        continue;
                    }

                    if ($segmentada['idPromocion'] > 0) {
                        $promo = $repo->find($segmentada['idPromocion']);

                        $promo->setIdProducto($producto)
                            ->setMinimo($segmentada['minimo'])
                            ->setIdTipoPromocion($tipoPromocion->findOneBy(['codigo' => $segmentada['tipo']]))
                            ->setFiltro($segmentada['filtroParse'])
                            ->setNombreFiltro($segmentada['filtro'])
                            ->setPoblacion($segmentada['poblacion']);


                        //Habria que añadir codigo necesario para guardar el filtro y hacer el calculo de la poblacion
                        //Se podria hacer con los Eventos de Symfony porque se puede utilizar en varias partes del codigo

                    } else {

                        $promo = new Promocion();

                        $promo->setIdProducto($producto)
                            ->setMinimo($segmentada['minimo'])
                            ->setIdTipoPromocion($tipoPromocion->findOneBy(['codigo' => $segmentada['tipo']]))
                            ->setNumPromocion($this->em->find('RMProductoBundle:NumPromociones', $idNumPro))
                            ->setTipo(Promocion::TIPO_SEGMENTADA)
                            ->setAceptada(Promocion::PENDIENTE)
                            ->setEstado(1)
                            ->setFiltro($segmentada['filtroParse'])
                            ->setNombreFiltro($segmentada['filtro'])
                            ->setPoblacion($segmentada['poblacion']);
                    }

                    $this->em->persist($promo);
                }

                $genericas = isset($promocion['genericas']) ? $promocion['genericas'] : [];
                foreach ($genericas as $generica) {

                    $producto = $this->em->find('RMProductoBundle:Producto', $generica['producto']);

                    if (!$producto) {
                        continue;
                    }

                    if ($generica['idPromocion'] > 0) {

                        $promo = $repo->find($generica['idPromocion']);

                        $promo->setIdProducto($producto)
                            ->setIdTipoPromocion($tipoPromocion->findOneBy(['codigo' => $generica['tipo']]));

                    } else {

                        $promo = new Promocion();

                        $promo->setIdProducto($producto)
                            ->setIdTipoPromocion($tipoPromocion->findOneBy(['codigo' => $generica['tipo']]))
                            ->setNumPromocion($this->em->find('RMProductoBundle:NumPromociones', $idNumPro))
                            ->setTipo(Promocion::TIPO_GENERICA)
                            ->setAceptada(Promocion::ACEPTADA)
                            ->setEstado(1);
                    }

                    $this->em->persist($promo);
                }
            }
        } catch (Exception $ex) {
            return 0;
        }

        $this->em->flush();
        $this->em->clear();

        return 1;
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