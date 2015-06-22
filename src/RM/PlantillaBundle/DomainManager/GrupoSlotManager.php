<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/04/2015
 * Time: 12:50
 */

namespace RM\PlantillaBundle\DomainManager;


use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Event\GrupoSlotsEvent;
use RM\PlantillaBundle\Event\GrupoSlotsEvents;


class GrupoSlotManager extends AbstractManager
{
    public function find($id)
    {
        $this->em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id);
    }

    public function update(GrupoSlots $grupo)
    {
        $this->save($grupo);
    }

    public function save(GrupoSlots $grupo)
    {
        $this->em->persist($grupo);
        $this->em->flush();
    }

    public function delete(GrupoSlots $grupo)
    {
        $grupo->setEstado(-1);
        $this->save($grupo);
        $this->dispatcher->dispatch(GrupoSlotsEvents::ELIMINAR_GRUPO_SLOTS, new GrupoSlotsEvent($grupo));
    }


    public function create(GrupoSlots $grupo)
    {
        $this->save($grupo);
        $this->dispatcher->dispatch(GrupoSlotsEvents::NUEVO_GRUPO_SLOTS, new GrupoSlotsEvent($grupo));
    }

    public function deleteByIds($ids = [])
    {
        if(empty($ids)) {
            return false;
        }

        $entities = $this->em->getRepository('RMPlantillaBundle:GrupoSlots')->findByIdGrupo($ids);

        foreach ($entities as $grupo) {
            $this->delete($grupo);
        }

        return true;

    }


} 