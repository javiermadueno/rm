<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 25/06/2015
 * Time: 9:57
 */

namespace RM\PlantillaBundle\EventListeners;


use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\PlantillaBundle\Event\GrupoSlotsEvent;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\ProductoBundle\Manager\NumPromocionesManager;

class EliminaNumPromocionesListener
{
    public function __construct(NumPromocionesManager $manager)
    {
        $this->manager = $manager;
    }
    public function eliminaNumPromociones(GrupoSlotsEvent $event)
    {
        $grupo = $event->getGrupoSlots();

        if(!$grupo instanceof GrupoSlotsInterface) {
            return;
        }

        $numPromociones = $this->em
            ->getRepository('RMProductoBundle:NumPromociones')
            ->findBy(['idGrupo' => $grupo]);

        $numPromociones = new ArrayCollection($numPromociones);

        foreach($numPromociones as $numPro) {
            $this->manager->remove($numPro);
        }
    }
} 