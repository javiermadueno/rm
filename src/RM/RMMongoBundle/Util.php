<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 08/05/2015
 * Time: 10:48
 */

namespace RM\RMMongoBundle;


class Util
{
    /**
     * Convierte un array multidimensional en un array unidemensional
     * que contiene todos las 'keys' y 'values' del anterior.
     *
     * @param   array $array
     * @param   array $return
     *
     * @return  array
     */
    static function array_flatten($array, $return)
    {
        foreach ($array as $key => $elem) {
            if (is_string($key)) {
                $return[] = $key;
            }

            if (is_array($elem)) {
                $return = self::array_flatten($elem, $return);
            } else {
                $return[] = $elem;
            }
        }
        return $return;
    }

    /**
     * @param     $fechaInicio
     * @param int $numeroMeses
     *
     * @return array
     */
    static function getUltimosMeses(\Datetime $fechaInicio, $numeroMeses = 1)
    {
        //$fecha = new \DateTime('first day of this month');

        if ($fechaInicio) {
            $fecha = $fechaInicio;
        } else {
            $fecha = new \DateTime('first day of this month');
        }

        $intervalo_1_mes = \DateInterval::createFromDateString('-1 month');
        $periodo = new \DatePeriod($fecha, $intervalo_1_mes, $numeroMeses);

        $meses = [];

        /** @var \Datetime $mes */
        foreach ($periodo as $mes) {
            $meses[] = $mes->format('Y-m');
        }

        return $meses;
    }
} 