<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 09/02/2015
 * Time: 11:37
 */

namespace RM\ComunicacionBundle\Event;


use RM\ComunicacionBundle\Entity\Comunicacion;
use Symfony\Component\EventDispatcher\Event;

class ComunicacionEvent extends Event
{
    private $comunicacion;

    public function __construct(Comunicacion $comunicacion)
    {
        $this->comunicacion = $comunicacion;
    }

    public function getComunicacion()
    {
        return $this->comunicacion;
    }
} 