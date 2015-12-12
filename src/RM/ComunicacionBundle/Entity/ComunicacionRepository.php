<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ComunicacionRepository extends EntityRepository
{
    /**
     * @param null $canal
     * @param null $estado
     *
     * @return Comunicacion[]
     */
    public function findByCanalYEstado($canal = null, $estado = null)
    {
        $qb = $this->createQueryBuilder('c')
                   ->addOrderBy('c.idComunicacion', 'ASC')
        ;

        if(!empty($estado)) {
            $qb->andWhere('c.estado = :estado')
               ->setParameter('estado', $estado);
        } else {
            $qb->andWhere('c.estado > -1');
        }

        if(!empty($canal)) {
            $qb->andWhere('c.idCanal = :canal')
               ->setParameter('canal', $canal);
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * @return Comunicacion[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado > -1')
            ->orderBy('c.idComunicacion', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id_comunicacion
     *
     * @return Comunicacion[]
     */
	public function deleteComunicaciones($id_comunicacion)
    {
        if(!is_array($id_comunicacion)) {
            $id_comunicacion = [$id_comunicacion];
        }

        return $this->createQueryBuilder('c')
            ->update()
            ->set('c.estado', -1)
            ->where('c.idComunicacion IN (:comunicacion)')
            ->setParameter('comunicacion', $id_comunicacion)
            ->getQuery()->getResult()
        ;
	}


    /**
     * @param $id
     *
     * @return Comunicacion
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        return $this->createQueryBuilder('c')
            ->join('c.plantilla', 'plantilla')->addSelect('plantilla')
            ->join('c.idCanal', 'canal')
            ->where('c.idComunicacion = :id')
            ->andWhere('c.estado > -1')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @param $plantilla
     *
     * @return array
     */
    public function findByPlantilla($plantilla)
    {
        return $this->createQueryBuilder('c')
            ->where('c.estado > -1')
            ->andWhere('c.plantilla = :plantilla')
            ->andWhere('c.generada = 1')
            ->setParameter('plantilla', $plantilla)
            ->getQuery()->getResult();
    }

	
}