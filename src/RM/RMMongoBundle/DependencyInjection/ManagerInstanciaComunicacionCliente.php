<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 06/03/2015
 * Time: 13:34
 */

namespace RM\RMMongoBundle\DependencyInjection;


use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use RM\ProductoBundle\Entity\Promocion;
use RM\RMMongoBundle\Document\InstanciaComunicacionCliente;

class ManagerInstanciaComunicacionCliente
{
    public function __construct(ManagerRegistry $doctrine, DocumentManager $mongo)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
        $this->mongo = $mongo;
    }

    /**
     * @param $idSlot
     * @param $idCliente
     *
     * @return Promocion
     * @throws \Exception
     */
    public function findPromocionBySlotyCliente($idSlot, $idCliente)
    {
        $instanciaComunicacionCliente = $this->mongo
            ->getRepository('RMMongoBundle:InstanciaComunicacionCliente')->findOneBy([
                'id_slot'    => $idSlot,
                'id_cliente' => $idCliente
            ]);

        if (!$instanciaComunicacionCliente instanceof InstanciaComunicacionCliente) {
            throw new \Exception(
                sprintf('El cliente %s no tiene asignada ninguna promoción para el slot %s', $idCliente, $idSlot)
            );
        }

        $promocion = $this->em->getRepository('RMProductoBundle:Promocion')
            ->find($instanciaComunicacionCliente->getPromocion());

        if (!$promocion instanceof Promocion) {
            throw new \Exception(
                sprintf("No existe la promoción %s", $instanciaComunicacionCliente->getPromocion())
            );
        }

        return $promocion;
    }


}