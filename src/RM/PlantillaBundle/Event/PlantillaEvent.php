<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 17:32
 */

namespace RM\PlantillaBundle\Event;


use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\EventDispatcher\Event;

class PlantillaEvent extends Event
{
    /**
     * @var PlantillaInterface
     */
    private $plantilla;

    /**
     * @param PlantillaInterface $plantilla
     */
    public function __construct(PlantillaInterface $plantilla)
    {
        $this->plantilla = $plantilla;
    }

    /**
     * @return PlantillaInterface
     */
    public function getPlantilla()
    {
        return $this->plantilla;
    }
} 