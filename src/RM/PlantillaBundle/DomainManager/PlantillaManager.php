<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/04/2015
 * Time: 11:07
 */

namespace RM\PlantillaBundle\DomainManager;

use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Event\PlantillaEvent;
use RM\PlantillaBundle\Event\PlantillaEvents;

class PlantillaManager extends AbstractManager
{

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->em->getRepository('RMPlantillaBundle:Plantilla')->find($id);
    }

    /**
     * @param Plantilla $plantilla
     */
    public function create(Plantilla $plantilla)
    {
        $plantilla->setEsModelo(false);

        $this->save($plantilla);
        $this->dispatcher->dispatch(PlantillaEvents::NUEVA_PLANTILLA, new PlantillaEvent($plantilla));
    }

    /**
     * @param Plantilla $plantilla
     */
    public function save(Plantilla $plantilla)
    {
        $this->em->persist($plantilla);
        $this->em->flush();
    }

    public function update(Plantilla $plantilla)
    {
        $this->save($plantilla);
    }

    /**
     * @param Plantilla $plantilla
     */
    public function export(Plantilla $plantilla)
    {
        $plantilla->setEsModelo(true);

        $this->save($plantilla);
    }

} 