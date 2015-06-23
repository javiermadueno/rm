<?php

namespace RM\ClienteBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;

/**
 * Class ClienteServicio
 *
 * @package RM\ClienteBundle\DependencyInjection
 */
class ClienteServicio
{
    /**
     * @param DoctrineManager $manager
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
    }

    /**
     * @return array
     */
    public function getCanales()
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registros = $repo->obtenerCanales();

        return $registros;
    }

    /**
     * @return array
     */
    public function getClientes()
    {

        $repo      = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClientes();

        return $registros;
    }

    /**
     * @param $id_cliente
     *
     * @return array
     */
    public function getClienteById($id_cliente)
    {

        $repo      = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClienteById($id_cliente);

        return $registros;
    }

    /**
     * @param $id_segmento
     *
     * @return array
     */
    public function getClientesBySegmento($id_segmento)
    {

        $repo      = $this->em->getRepository('RMClienteBundle:Cliente');
        $registros = $repo->obtenerClienteBySegmento($id_segmento);

        return $registros;
    }
}