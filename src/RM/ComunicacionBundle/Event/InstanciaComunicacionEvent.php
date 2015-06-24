<?php

namespace RM\ComunicacionBundle\Event;


use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use Symfony\Component\EventDispatcher\Event;

class InstanciaComunicacionEvent extends Event {

    private $instancia;

    public function __construct(InstanciaComunicacion $instancia)
    {
        $this->instancia = $instancia;
    }

    public function getInstancia()
    {
        return $this->instancia;
    }
} 