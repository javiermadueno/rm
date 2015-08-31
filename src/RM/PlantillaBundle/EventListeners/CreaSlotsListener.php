<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 26/02/2015
 * Time: 15:41
 */

namespace RM\PlantillaBundle\EventListeners;

use RM\AppBundle\DependencyInjection\DoctrineManager;
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
     * @param DoctrineManager $manager
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager)
    {
        try {
            $this->em = $manager->getManager();
        }catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param GrupoSlotsEvent $event
     */
    public function onNuevoGrupoSlot(GrupoSlotsEvent $event)
    {
        $grupo = $event->getGrupoSlots();

        if(!$grupo instanceof GrupoSlotsInterface) {
            return;
        }
        $numSlots = (int) $grupo->getNumSlots();

        if($numSlots <= 0) {
            return;
        }

        for($i = 0; $i < $numSlots; $i++)
        {
            $slot = new Slot();

            $slot->setIdGrupo($grupo)
                ->setCodigo(uniqid($grupo->getIdGrupo() . '_'))
                ->setEstado(1);

            $this->em->persist($slot);
        }

        $this->em->flush();

    }
} 