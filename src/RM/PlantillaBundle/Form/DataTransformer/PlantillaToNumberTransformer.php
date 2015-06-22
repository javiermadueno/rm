<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 11:31
 */

namespace RM\PlantillaBundle\Form\DataTransformer;

use RM\PlantillaBundle\Entity\Plantilla;


class PlantillaToNumberTransformer extends EntityToNumberTransformer
{

    protected $entityClass = 'RM\PlantillaBundle\Entity\Plantilla';

    protected $entityRepository = 'RMPlantillaBundle:Plantilla';

    /**
     * @param Object|Plantilla $entity
     *
     * @return int
     */
    protected function getId($entity)
    {
        return $entity->getIdPlantilla();
    }

    /**
     * @param int $id
     *
     * @return Object|Plantilla
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }

} 