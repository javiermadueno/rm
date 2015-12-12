<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/11/2015
 * Time: 10:14
 */

namespace RM\InsightBundle\Filter;

use RM\ComunicacionBundle\Entity\Comunicacion;


class InstanciaComunicacionFilter extends AbstractFilter
{

    /**
     * @var Comunicacion
     */
    private $comunicacion;

    /**
     * @var \DateTime
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     */
    private $fechaFin;

    /**
     * @return Comunicacion
     */
    public function getComunicacion()
    {
        return $this->comunicacion;
    }

    /**
     * @param Comunicacion $comunicacion
     */
    public function setComunicacion(Comunicacion $comunicacion = null)
    {
        $this->comunicacion = $comunicacion;
    }

    /**
     * @return \DateTime
     */
    public function getFechaFin()
    {
        if($this->fechaFin instanceof \DateTime) {
            return $this->fechaFin->setTime(0,0,0);
        }

        return null;

    }

    /**
     * @param \DateTime $fechaFin
     */
    public function setFechaFin(\DateTime $fechaFin = null)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        if($this->fechaInicio instanceof \DateTime) {
            return $this->fechaInicio->setTime(0,0,0);
        }

        return null;
    }

    /**
     * @param \DateTime $fechaInicio
     */
    public function setFechaInicio(\DateTime $fechaInicio = null)
    {
        $this->fechaInicio = $fechaInicio;
    }

} 