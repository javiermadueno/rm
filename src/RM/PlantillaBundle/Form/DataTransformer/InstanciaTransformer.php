<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/04/2015
 * Time: 16:34
 */

namespace RM\PlantillaBundle\Form\DataTransformer;

use RM\ComunicacionBundle\Entity\InstanciaComunicacion;

class InstanciaTransformer extends EntityToNumberTransformer
{
    protected $entityClass = 'RM\ComunicacionBundle\Entity\InstanciaComunicacion';

    protected $entityRepository = 'RMComunicacionBundle:InstanciaComunicacion';

    /**
     * @param Object|InstanciaComunicacion $entity
     *
     * @return int
     */
    protected function getId($entity)
    {
        return $entity->getIdInstancia();
    }

    /**
     * @param int $id
     *
     * @return Object|InstanciaComunicacion
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }
} 