<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 17/02/2015
 * Time: 10:33
 */

namespace RM\PlantillaBundle\Form\DataTransformer;


use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;

class GrupoSlotsToNumberTransformer extends EntityToNumberTransformer
{
    protected $entityClass = 'RM\PlantillaBundle\Entity\GrupoSlots';

    protected $entityRepository = 'RMPlantillaBundle:GrupoSlots';

    /**
     * @param Object|GrupoSlotsInterface $entity
     * @return int
     */
    protected function getId($entity)
    {
        return $entity->getIdGrupo();
    }

    /**
     * @param int $id
     * @return Object|GrupoSlotsInterface
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }
} 