<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/05/2015
 * Time: 10:27
 */

namespace RM\DiscretasBundle\Entity;


use Doctrine\ORM\EntityRepository;

class ParametroConfiguracionRepository extends EntityRepository
{
    public function findParametrosConfiguracionByNivelAmplitud($nivelAmplitud = 1)
    {
        $parametros  = $this->createQueryBuilder('p')
            ->where('p.codigo NOT LIKE :codigo_amplitud')
            ->setParameter('codigo_amplitud', 'amp%')
            ->getQuery()->getResult();


        $parametro_amplitud = $this->createQueryBuilder('p')
            ->where('p.codigo = :codigo_amplitud')
            ->setParameter('codigo_amplitud', sprintf('amp%s', $nivelAmplitud))
            ->getQuery()->getResult();

        $parametros = array_merge($parametros, $parametro_amplitud);

        return $parametros;

    }
} 