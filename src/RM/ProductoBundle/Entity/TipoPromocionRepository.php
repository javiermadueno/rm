<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TipoPromocionRepository extends EntityRepository
{
    public function obtenerTipos()
    {

        $dql = "select m
		from RMProductoBundle:TipoPromocion m";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;

    }
}