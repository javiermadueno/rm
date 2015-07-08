<?php

namespace RM\ComunicacionBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;

class CanalServicio
{

    private $em;

    /**
     * @param DoctrineManager $manager
     *
     * @throws \Exception
     */
    public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
    }

    public function getCanales()
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registros = $repo->obtenerCanales();

        return $registros;
    }

    public function getCanalById($id_canal)
    {
        $repo     = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registro = $repo->obtenerCanalById($id_canal);

        return $registro;
    }

}