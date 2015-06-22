<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ConfiguracionRepository extends EntityRepository
{

    public function getConfigurationParameters()
    {
        $dql = "
            SELECT c
			FROM RMDiscretasBundle:Configuracion c
			";

        $query = $this->_em->createQuery($dql);
        $registros = $query->getResult();

        return $registros;
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