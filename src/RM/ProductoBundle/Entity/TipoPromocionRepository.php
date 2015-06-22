<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TipoPromocionRepository extends EntityRepository
{
    public function obtenerTipos()
    {

        //$em = $this->getEntityManager();
        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "select m
		from RMProductoBundle:TipoPromocion m";

        $query = $em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;

    }
}