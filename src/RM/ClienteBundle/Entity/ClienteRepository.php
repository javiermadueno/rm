<?php

namespace RM\ClienteBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClienteRepository extends EntityRepository
{
    public function obtenerPlantillaById($id_plantilla)
    {

        //$em = $this->getEntityManager();
        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "SELECT p
            FROM RMPlantillaBundle:Plantilla p
			WHERE p.idPlantilla = :idplantilla
			AND	  p.estado > -1";

        $query = $em->createQuery($dql);
        $query->setParameter('idplantilla', $id_plantilla);

        $registros = $query->getResult();

        return $registros;
    }

    //TODO Los devuelve todos
    public function obtenerClientes()
    {

        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "SELECT c
            FROM RMClienteBundle:Cliente c";

        $query = $em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;
    }

    //TODO Los devuelve todos
    public function obtenerClienteById($id_cliente)
    {

        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "SELECT c
            FROM RMClienteBundle:Cliente c
			WHERE c.idCliente = :idcliente";

        $query = $em->createQuery($dql);
        $query->setParameter('idcliente', $id_cliente);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerClienteBySegmento($id_segmento)
    {

        $em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager($_SESSION['connection']);

        $dql = "select c
		from RMClienteBundle:Cliente c
		JOIN RMClienteBundle:ClienteSegmento cs WITH (c.idCliente = cs.idCliente)
		JOIN RMSegmentoBundle:Segmento s WITH (s.idSegmento = cs.idSegmento)	
		WHERE cs.idSegmento = :idSegmento";


        $query = $em->createQuery($dql);
        $query->setParameter('idSegmento', $id_segmento);

        $registros = $query->getResult();

        return $registros;

    }

    public function findClientesByIds(array $ids)
    {
        $clientes = $this->createQueryBuilder('c')
            ->select('c.idCliente')
            ->addSelect('c.nombre')
            ->addSelect('c.apellidos')
            ->where('c.idCliente IN (:ids)')
            ->andWhere('c.estado > -1')
            ->orderBy('c.idCliente')
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
        //->getResult();

        return $clientes;

    }
}