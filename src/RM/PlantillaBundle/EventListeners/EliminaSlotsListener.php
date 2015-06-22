<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 17:48
 */

namespace RM\PlantillaBundle\EventListeners;


use Doctrine\Common\Persistence\ManagerRegistry;
use RM\PlantillaBundle\Event\GrupoSlotsEvent;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;

class EliminaSlotsListener
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        if (!isset($_SESSION['connection'])) {
            return;
        }

        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function onGrupoSlotsEliminado(GrupoSlotsEvent $event)
    {
        $grupo = $event->getGrupoSlots();

        if (!$grupo instanceof GrupoSlotsInterface) {
            return;
        }

        $slots = $grupo->getSlots();

        $this->eliminarSlots($slots);
    }

    private function eliminarSlots($slots)
    {
        foreach ($slots as $slot) {
            $slot->setEstado(-1);
            $this->em->persist($slot);
        }

        $this->em->flush();

    }
} 