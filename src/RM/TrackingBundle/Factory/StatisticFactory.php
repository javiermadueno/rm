<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/12/2015
 * Time: 12:54
 */

namespace RM\TrackingBundle\Factory;


use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\TrackingBundle\Document\TrackingEmail;
use RM\TrackingBundle\Model\EmailCampaignStatistics;
use RM\TrackingBundle\Repository\TrackingEmailRepository;

class StatisticFactory
{

    /**
     * @var TrackingEmailRepository
     */
    private $repository;

    public function __construct(TrackingEmailRepository $repository)
    {
        $this->repository =  $repository;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return EmailCampaignStatistics
     */
    public function createStatisticsFor(InstanciaComunicacion $instancia)
    {
        $ratios = $this->repository->findOpenAndClickRate($instancia);

        $eventosHora = $this->repository->getNumeroEventosPorHora($instancia);

        return new EmailCampaignStatistics($instancia, $ratios, $eventosHora);
    }
} 