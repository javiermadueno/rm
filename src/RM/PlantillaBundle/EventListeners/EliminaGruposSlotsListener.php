<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 17:40
 */

namespace RM\PlantillaBundle\EventListeners;

use RM\PlantillaBundle\DomainManager\GrupoSlotManager;
use RM\PlantillaBundle\Event\PlantillaEvent;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;

class EliminaGruposSlotsListener
{
    /**
     * @var GrupoSlotManager
     */
    private $manager;

    public function __construct(GrupoSlotManager $manager)
    {
        $this->manager = $manager;
    }

    public function onPlantillaEliminada(PlantillaEvent $event)
    {
        $plantilla = $event->getPlantilla();

        if (!$plantilla instanceof PlantillaInterface) {
            return;
        }

        $grupos = $plantilla->getGruposSlots();
        $this->eliminaGrupos($grupos);

    }

    private function eliminaGrupos($grupos)
    {
        /** @var GrupoSlotsInterface $grupo */
        foreach ($grupos as $grupo) {
            $this->manager->delete($grupo);
        }

    }
} 