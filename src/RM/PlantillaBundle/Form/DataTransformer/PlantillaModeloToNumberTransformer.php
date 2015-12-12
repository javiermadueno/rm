<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 17/02/2015
 * Time: 10:56
 */

namespace RM\PlantillaBundle\Form\DataTransformer;

use RM\PlantillaBundle\Entity\Plantilla;


class PlantillaModeloToNumberTransformer extends EntityToNumberTransformer
{
    protected $entityClass = 'RM\PlantillaBundle\Entity\Plantilla';

    protected $entityRepository = 'RMPlantillaBundle:Plantilla';

    /**
     * @param Object|Plantilla $entity
     * @return int
     */
    protected function getId($entity)
    {
        return $entity->getIdPlantilla();
    }

    /**
     * @param int $id
     * @return Object|Plantilla
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }
} 