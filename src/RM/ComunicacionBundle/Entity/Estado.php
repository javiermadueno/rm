<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 28/01/2015
 * Time: 15:19
 */

namespace RM\ComunicacionBundle\Entity;


class Estado
{

    const ELIMINADO = -1;
    const CONFIGURACION = 0;
    const ACTIVO = 1;
    const PAUSADO = 2;
    const COMPLETADA = 3;
} 