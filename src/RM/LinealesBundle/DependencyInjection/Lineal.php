<?php

namespace RM\LinealesBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;


class Lineal
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function getLineales($nombre = '', $tipoVar = 0)
    {
        //$registros = $this->em->find('RMLinealesBundle:Vil',$tipoVar);
        $registros = $this->em->getRepository('RMLinealesBundle:Vil')->obtenerVariablesLineales($nombre, $tipoVar);
        return $registros;

    }

    public function getVariablesLinealesNoSociodemograficas()
    {
        return $this->em->getRepository('RMLinealesBundle:Vil')
            ->obetenerVariablesLinealesNoSociodemograficas();
    }
}