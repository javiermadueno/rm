<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 17/02/2015
 * Time: 10:56
 */

namespace RM\PlantillaBundle\Form\DataTransformer;

use RM\PlantillaBundle\Entity\PlantillaModelo;


class PlantillaModeloToNumberTransformer extends EntityToNumberTransformer
{
    protected $entityClass = 'RM\PlantillaBundle\Entity\PlantillaModelo';

    protected $entityRepository = 'RMPlantillaBundle:PlantillaModelo';

    /**
     * @param Object|PlantillaModelo $entity
     * @return int
     */
    protected function getId($entity)
    {
        return $entity->getIdPlantilla();
    }

    /**
     * @param int $id
     * @return Object|PlantillaModelo
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }
} 