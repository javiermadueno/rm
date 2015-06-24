<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 24/02/2015
 * Time: 17:05
 */

namespace RM\PlantillaBundle\EventListeners;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;


class ModificaNumeroSlotsListener
{
    public function postUpdate(LifecycleEventArgs $event)
    {
        $grupoSlots = $event->getEntity();

        if(!$grupoSlots instanceof GrupoSlotsInterface) {
            return;
        }

        $em = $event->getEntityManager();

        $numSlotsPreUpdate  = $grupoSlots->getSlots()->count();
        $numSlotsPostUpdate = $grupoSlots->getNumSlots();

        if($numSlotsPostUpdate == $numSlotsPreUpdate) {
            return;
        }

        if($numSlotsPostUpdate > $numSlotsPreUpdate) {
            $numSlotsNuevos = $numSlotsPostUpdate - $numSlotsPreUpdate;

            for($i=0; $i<$numSlotsNuevos; $i++) {
                $slot = new Slot();

                $slot->setEstado(1)
                    ->setIdGrupo($grupoSlots)
                    ->setCodigo(uniqid($grupoSlots->getIdGrupo().'_'));

                $em->persist($slot);
            }

            $em->flush();
            return;
        }

        if($numSlotsPostUpdate < $numSlotsPreUpdate) {
            $numSlotsABorrar = $numSlotsPreUpdate - $numSlotsPostUpdate;
            /** @var ArrayCollection $slots */
            $slots = $grupoSlots->getSlots();

            $slots->last();
            while($numSlotsABorrar) {
                $slot = $slots->last();
                $em->remove($slot);
                $slots->removeElement($slot);

                $numSlotsABorrar--;
            }

            $em->flush();
        }

    }


} 