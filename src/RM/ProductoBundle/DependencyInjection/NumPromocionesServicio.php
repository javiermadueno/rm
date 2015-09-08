<?php

namespace RM\ProductoBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ProductoBundle\Entity\NumPromocionesRepository;


class NumPromocionesServicio
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @param DoctrineManager $doctrine
     *
     * @throws \Exception
     */
	public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * @param int $id_categoria
     * @param int $id_grupo
     * @param int $id_instancia
     * @return mixed
     */
    public function getNumPromocionesByFiltros($id_categoria = -1, $id_grupo = -1, $id_instancia = -1)
    {
        /** @var NumPromocionesRepository $repo */
    	$repo = $this->em->getRepository('RMProductoBundle:NumPromociones');
    	$registros = $repo->obtenerNumPromocionesByFiltros($id_categoria, $id_grupo, $id_instancia);
    	return $registros;
    
    }

    /**
     * @param int $id_grupo
     * @param int $id_instancia
     * @return mixed
     */
    public function getNumPromocionesCreatividadByFiltros($id_grupo = -1, $id_instancia = -1)
    {
        /** @var NumPromocionesRepository $repo */
        $repo = $this->em->getRepository('RMProductoBundle:NumPromociones');
        $registros = $repo->obtenerNumPromocionesCreatividadByFiltros($id_grupo, $id_instancia);
        return $registros;

    }

    /**
     * @param $id_categoria
     * @param $id_instancia
     * @return mixed
     */
    public function getNumPromocionesCampanyaByFiltros($id_categoria, $id_instancia)
    {
        /** @var NumPromocionesRepository $repo */
    	$repo = $this->em->getRepository('RMProductoBundle:NumPromociones');
    	$registros = $repo->obtenerNumPromocionesCampanyaByFiltros($id_categoria, $id_instancia);
    	return $registros;
    
    }
}