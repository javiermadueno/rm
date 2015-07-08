<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 17:48
 */

namespace RM\PlantillaBundle\EventListeners;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\PlantillaBundle\Event\GrupoSlotsEvent;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;

class EliminaSlotsListener
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    public function __construct(DoctrineManager $manager)
    {
        try {
            $this->em = $manager->getManager();
        }catch (\Exception $e) {
            return;
        }
    }

    public function onGrupoSlotsEliminado(GrupoSlotsEvent $event)
    {
        $grupo = $event->getGrupoSlots();

        if(!$grupo instanceof GrupoSlotsInterface) {
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