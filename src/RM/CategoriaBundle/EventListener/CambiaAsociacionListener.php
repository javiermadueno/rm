<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 18/05/2015
 * Time: 12:03
 */

namespace RM\CategoriaBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use RM\CategoriaBundle\Entity\NivelCategoria;

class CambiaAsociacionListener
{

    public function postUpdate(LifecycleEventArgs $event)
    {
        $nivel = $event->getEntity();

        if(!$nivel instanceof NivelCategoria) {
            return;
        }

        $categoria_repository = $event
            ->getEntityManager()
            ->getRepository('RMCategoriaBundle:Categoria');

        $categoria_repository
            ->updateAsociacionCategoriasByNivel($nivel->getIdNivelCategoria(), $nivel->getAsociado());


    }

} 