<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 04/12/2015
 * Time: 9:06
 */

namespace RM\TrackingBundle\Model;


use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\TrackingBundle\Document\TrackingEmail;

class EmailCampaignStatistics extends CampaingStatistics
{
    /**
     * @var InstanciaComunicacion
     */
    private $instancia;

    /**
     * @var EventsCount
     */
    private $eventos;

    /**
     * @var EventsHourCount
     */
    private $eventosPorHora;

    public function __construct(InstanciaComunicacion $instancia, EventsCount $eventos, EventsHourCount $eventosPorHora)
    {
        $this->instancia = $instancia;
        $this->eventos   = $eventos;
        $this->eventosPorHora = $eventosPorHora;
    }

    /**
     * @return InstanciaComunicacion
     */
    public function getInstancia()
    {
        return $this->instancia;
    }

    /**
     * @param InstanciaComunicacion $instancia
     */
    public function setInstancia(InstanciaComunicacion $instancia)
    {
        $this->instancia = $instancia;
    }

    /**
     * @return float|int
     */
    public function getRatioApertura()
    {
        return $this->getRatio(TrackingEmail::OPEN);
    }

    /**
     * @return float|int
     */
    public function getRatioClick()
    {
        return $this->getRatio(TrackingEmail::CLICK);
    }

    /**
     * @param $tipo
     *
     * @return float|int
     */
    public function getRatio($tipo)
    {
        return $this->calculaRatio(
            $this->getTotalEvents($tipo),
            $this->getNumEnvios()
        );
    }

    /**
     * @param $num
     * @param $total
     *
     * @return float|int
     */
    private function calculaRatio($num, $total)
    {
        if (0 == $total) {
            return 0;
        }

        return ($num / $total) * 100;
    }

    /**
     * @return int
     */
    public function getNumEnvios()
    {
        return $this
            ->instancia
            ->getNumComunicaciones();
    }

    /**
     * @return \DateTime
     */
    public function getFechaEnvio()
    {
        return $this
            ->instancia
            ->getFechaEnvio();
    }

    /**
     * @param $tipo
     *
     * @return int
     */
    public function getTotalEvents($tipo)
    {
        return $this
            ->eventos
            ->getEventCount($tipo)
            ;
    }

    /**
     * Devuelve un array con el numero de eventos ordenados por hora
     *
     * [..., 14 => 250, 15 => 123, ...]
     *
     * @param $tipo
     *
     * @return array|bool
     */
    public function getTotalEventsByHour($tipo)
    {
        return $this
            ->eventosPorHora
            ->getEventsByHour($tipo)
            ;
    }


} 