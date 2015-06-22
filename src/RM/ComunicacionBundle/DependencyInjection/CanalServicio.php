<?php

namespace RM\ComunicacionBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;

class CanalServicio
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function getCanales()
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registros = $repo->obtenerCanales();
        return $registros;
    }

    public function getCanalById($id_canal)
    {
        $repo = $this->em->getRepository('RMComunicacionBundle:Canal');
        $registro = $repo->obtenerCanalById($id_canal);
        return $registro;
    }

}