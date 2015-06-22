<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 31/03/2015
 * Time: 11:32
 */

namespace RM\DiscretasBundle\Entity;


use Doctrine\ORM\EntityRepository;

class VidGrupoSegmentoRepository extends EntityRepository
{
    /**
     * @param $idVid
     *
     * @return VidGrupoSegmento|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastGrupoSegmentoActivo($idVid)
    {
        $last_entity = $this->_em->createQueryBuilder()
            ->select('g')
            ->from('RMDiscretasBundle:VidGrupoSegmento', 'g')
            ->where('g.idVid = :idVid')
            ->andWhere('g.estado > -1')
            ->orderBy('g.idVidGrupoSegmento', 'DESC')
            ->setMaxResults(1)
            ->setParameter('idVid', $idVid)
            ->getQuery()
            ->getOneOrNullResult();

        return $last_entity;
    }
} 