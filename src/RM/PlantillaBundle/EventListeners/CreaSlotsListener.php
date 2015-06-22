<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 26/02/2015
 * Time: 15:41
 */

namespace RM\PlantillaBundle\EventListeners;

use Doctrine\Common\Persistence\ManagerRegistry;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Event\GrupoSlotsEvent;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;

class CreaSlotsListener
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        if (!isset($_SESSION['connection'])) {
            return;
        }

        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    /**
     * @param GrupoSlotsEvent $event
     */
    public function onNuevoGrupoSlot(GrupoSlotsEvent $event)
    {
        $grupo = $event->getGrupoSlots();

        if (!$grupo instanceof GrupoSlotsInterface) {
            return;
        }
        $numSlots = (int)$grupo->getNumSlots();

        if ($numSlots <= 0) {
            return;
        }

        for ($i = 0; $i < $numSlots; $i++) {
            $slot = new Slot();

            $slot->setIdGrupo($grupo)
                ->setCodigo(uniqid($grupo->getIdGrupo() . '_'))
                ->setEstado(1);

            $this->em->persist($slot);
        }

        $this->em->flush();

    }
} 