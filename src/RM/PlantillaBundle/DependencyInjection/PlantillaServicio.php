<?php

namespace RM\PlantillaBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\PlantillaBundle\Entity\Plantilla;


class PlantillaServicio
{
    /**
     * @param DoctrineManager $manager
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager)
    {
        $this->em = $em = $manager->getManager();
    }

    /**
     * @param $id_plantilla
     *
     * @return array
     */
    public function getPlantillaById($id_plantilla)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerPlantillaById($id_plantilla);

        return $registros;
    }


    /**
     * @param $id_plantilla
     *
     * @return array
     */
    public function getGSByIdPlantilla($id_plantilla)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerGSByIdPlantilla($id_plantilla);

        return $registros;
    }

    /**
     * @param $id_grupo
     *
     * @return array
     */
    public function getGSById($id_grupo)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerGSById($id_grupo);

        return $registros;
    }

    /**
     * @param $id_grupo
     *
     * @return array
     */
    public function getSlotByIdGrupo($id_grupo)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerSlotByIdGrupo($id_grupo);

        return $registros;
    }

    /**
     * @param $id_slot
     *
     * @return array
     */
    public function getSlotById($id_slot)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerSlotById($id_slot);

        return $registros;
    }

    /**
     * @param $id_plantilla
     *
     * @return array
     */
    public function getGruposConNumeroSlots($id_plantilla)
    {
        $repo = $this->em->getRepository('RMPlantillaBundle:Plantilla');
        $registros = $repo->obtenerGruposConNumeroSlots($id_plantilla);

        return $registros;
    }

}