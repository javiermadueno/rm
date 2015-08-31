<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/07/2015
 * Time: 10:01
 */

namespace RM\ComunicacionBundle\Entity;



use RM\ComunicacionBundle\Model\Abstracts\InstanciaDecoratorAbstract;
use RM\PlantillaBundle\Entity\GrupoSlots;


class Campaign extends InstanciaDecoratorAbstract
{
    /**
     * @return int
     */
    protected function getTipoNumPromocion()
    {
        return GrupoSlots::PROMOCION;
    }

} 