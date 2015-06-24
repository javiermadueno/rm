<?php

namespace RM\PlantillaBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\GrupoSlotsRepository;


class GrupoSlotsServicio
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var GrupoSlotsRepository
     */
    private $repo;

    /**
     * @param DoctrineManager $manager
     *
     * @throws \Exception
     */
	public function __construct(DoctrineManager $manager)
	{
		$this->em = $manager->getManager();
        $this->repo = $this->em->getRepository('RMPlantillaBundle:GrupoSlots');
	}

    /**
     * @param $id_grupoSlots
     * @return mixed
     */
	public function getGrupoSlotsById($id_grupoSlots)
    {
		$registros = $this->repo->obtenerGrupoSlotsById($id_grupoSlots);
		return $registros;
	}

    /**
     * @param $listaGSAEliminar
     * @return array
     */
	public function eliminarGrupoSlotsByIds($listaGSAEliminar)
    {
	    $registrosGS = [];

		foreach ($listaGSAEliminar as $GS){
				
			$registrosGS = $this->repo->eliminarGSById( $GS);
		}
	
		$this->em->flush ();
		return $registrosGS;
	}

    /**
     * @param $id_instancia
     * @return mixed
     */
    public function getGrupoSlotsCreatividadConPromocion($id_instancia)
    {
        $objRegistros = $this->repo->obtenerGrupoSlotsCreatividadConPromocion($id_instancia);
        return $objRegistros;
    }

    /**
     * @param $id_instancia
     * @return mixed
     */
    public function getGrupoSlotsCreatividadPromocionConNumeroSlots($id_instancia)
    {
        $objRegistros = $this->repo->obtenerGrupoSlotsCreatividadPromocionConNumeroSlots($id_instancia);
        return $objRegistros;
    }

}