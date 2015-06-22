<?php

namespace RM\ClienteBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;

class ClienteServicio
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCanales()
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registros = $repo->obtenerCanales();
        return $registros;
    }

    public function getClientes()
    {

        $repo = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClientes();
        return $registros;
    }

    public function getClienteById($id_cliente)
    {

        $repo = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClienteById($id_cliente);
        return $registros;
    }

    public function getClientesBySegmento($id_segmento)
    {

        $repo = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClienteBySegmento($id_segmento);
        return $registros;
    }
}