<?php

namespace RM\TrackingBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;

class TrackingEmailRpository extends DocumentRepository
{

    public function findNumeroAperturasByInstancia(InstanciaComunicacion $instancia)
    {
        return  $this
            ->createQueryBuilder()
            ->field('instancia')->equals($instancia->getIdInstancia())
            ->field('tipo')->equals('email.open')
            ->distinct('cliente')
            ->count()
            ->getQuery()
            ->execute();
    }
}