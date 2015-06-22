<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 12:31
 */

namespace RM\PlantillaBundle\Model\Interfaces;


use RM\ComunicacionBundle\Entity\Canal;

interface PlantillaInterface
{
    public function setNombre($nombre);

    public function getNombre();

    public function setLienzoAncho($lienzoAncho);

    public function getLienzoAncho();

    public function setLienzoAlto($lienzoAlto);

    public function getLienzoAlto();

    public function setEstado($estado);

    public function getEstado();

    public function getIdPlantilla();

    public function getGruposSlots();

    public function getCanal();

    public function setCanal(Canal $canal);

    public function setDescripcion($descripcion);

    public function getDescripcion();

}