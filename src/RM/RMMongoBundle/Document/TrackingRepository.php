<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/10/2015
 * Time: 11:24
 */

namespace RM\RMMongoBundle\Document;


use Doctrine\ODM\MongoDB\DocumentRepository;

class TrackingRepository extends DocumentRepository
{

    /**
     * Actualiza en 1 el numero de veces que se ha abierto un email para la instancia dada.
     *
     * @param $instancia int
     *
     * @return bool
     */
    public function updateNumApertura($instancia, $cliente)
    {
        if(!is_numeric($instancia) || !is_numeric($cliente) ) {
            return false;
        }

        $now = new \DateTime();
        $now  = $now->getTimestamp();

        try {
            $this->createQueryBuilder()
                 ->update()
                 ->field('aperturas')->inc(1)
                 ->field('instancia')->equals($instancia)
                 ->field('cliente')->equals($cliente)
                 ->field('modified')->set(new \MongoDate($now))
                 ->upsert(true)
                 ->getQuery()
                 ->execute()
            ;

        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public function getResultadosCampana($instancia)
    {
        if(!is_numeric($instancia)) {
            return false;
        }

        $this->createQueryBuilder()
            ->field('instancia')->equals($instancia)
            ->count()
        ;
    }

} 