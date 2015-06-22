<?php

namespace RM\ProductoBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;


class TipoPromocionServicio
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getTipos()
    {
        $registros = $this->em->getRepository('RMProductoBundle:TipoPromocion')->obtenerTipos();
        return $registros;

    }
}