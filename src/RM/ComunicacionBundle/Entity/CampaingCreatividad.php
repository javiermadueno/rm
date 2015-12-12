<?php
/**
 * Created by PhpStorm.
 * User: javi
 * Date: 29/08/15
 * Time: 19:32
 */

namespace RM\ComunicacionBundle\Entity;


use RM\ComunicacionBundle\Model\Abstracts\InstanciaDecoratorAbstract;
use RM\PlantillaBundle\Entity\GrupoSlots;


class CampaingCreatividad extends InstanciaDecoratorAbstract
{

    /**
     * @return int
     */
    protected function getTipoNumPromocion()
    {
        return GrupoSlots::CREATIVIDADES;
    }

} 