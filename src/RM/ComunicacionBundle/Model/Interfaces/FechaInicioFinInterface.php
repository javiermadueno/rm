<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 13/02/2015
 * Time: 12:17
 */

namespace RM\ComunicacionBundle\Model\Interfaces;


interface FechaInicioFinInterface
{
    /**
     * @return \Date
     */
    public function getFecInicio();

    /**
     * @return \Date
     */
    public function getFecFin();

    /**
     * @param  $fechaInicio
     *
     * @return mixed
     */
    public function setFecFin($fechaInicio = null);

    /**
     * @param   $fechaFin
     *
     * @return  mixed
     */
    public function setFecInicio($fechaFin = null);
} 