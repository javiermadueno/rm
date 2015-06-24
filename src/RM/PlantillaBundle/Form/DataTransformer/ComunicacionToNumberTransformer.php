<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/02/2015
 * Time: 15:51
 */

namespace RM\PlantillaBundle\Form\DataTransformer;


use RM\ComunicacionBundle\Entity\Comunicacion;

class ComunicacionToNumberTransformer extends EntityToNumberTransformer
{

    protected $entityClass = 'RM\ComunicacionBundle\Entity\Comunicacion';

    protected $entityRepository = 'RMComunicacionBundle:Comunicacion';

    /**
     * @param Object|Comunicacion $comunicacion
     *
     * @return int
     */
    protected function getId($comunicacion)
    {
        return $comunicacion->getIdComunicacion();
    }

    /**
     * @param int $id
     *
     * @return Object|Comunicacion
     */
    protected function getEntity($id)
    {
        return $this->em->getRepository($this->entityRepository)->find($id);
    }

} 