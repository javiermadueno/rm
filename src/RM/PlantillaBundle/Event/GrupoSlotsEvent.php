<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 26/02/2015
 * Time: 15:42
 */

namespace RM\PlantillaBundle\Event;


use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use Symfony\Component\EventDispatcher\Event;

class GrupoSlotsEvent extends Event
{

    private $grupoSlots;

    public function __construct(GrupoSlotsInterface $grupo)
    {
        $this->grupoSlots = $grupo;
    }

    public function getGrupoSlots()
    {
        return $this->grupoSlots;
    }
} 