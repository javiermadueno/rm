<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 01/09/2015
 * Time: 12:30
 */

namespace RM\ComunicacionBundle\Form\DataTransformer;


use RM\PlantillaBundle\Form\DataTransformer\EntityToNumberTransformer;
use RM\ComunicacionBundle\Entity\Creatividad;

class CreatividadToStringTransformer extends EntityToNumberTransformer
{
    /**
     * @var string Clase de la entidad
     */
    protected  $entityClass = 'RM\ComunicacionBundle\Entity\Creatividad';

    /**
     * @var string Nombre del repositorio de la entidad
     */
    protected  $entityRepository = 'RMComunicacionBundle:Creatividad';

    public function getId($entity)
    {
        /** @var $entity Creatividad */
        return $entity->getIdCreatividad();
    }

    public function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }

} 