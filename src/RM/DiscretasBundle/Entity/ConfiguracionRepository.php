<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ConfiguracionRepository extends EntityRepository
{

    public function getConfigurationParameters()
    {
        $configuracion = $this->createQueryBuilder('c')
            ->where('c.tipo = :tipo')
            ->setParameter('tipo', Configuracion::GENERAL)
            ->getQuery()->getResult();


        return $configuracion;
    }

    public function findParametrosConfiguracionByTipo($tipo = Configuracion::GENERAL)
    {
        $configuracion = $this->createQueryBuilder('c')
            ->where('c.tipo = :tipo')
            ->setParameter('tipo', $tipo)
            ->getQuery()->getResult();


        return $configuracion;
    }

    /**
     * @return mixed
     */
    public function findNivelGamaASegmentar()
    {
        return $this->createQueryBuilder('c')
            ->select('c.valor')
            ->where('c.nombre = :amplitud')
            ->setParameter('amplitud', 'nivel_gama_segmentar')
            ->getQuery()->getSingleScalarResult();
    }

}