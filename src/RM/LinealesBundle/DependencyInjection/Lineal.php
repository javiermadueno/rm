<?php

namespace RM\LinealesBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;


class Lineal
{
    public function __construct(DoctrineManager $doctrine)
    {
        $this->em = $doctrine->getManager();
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