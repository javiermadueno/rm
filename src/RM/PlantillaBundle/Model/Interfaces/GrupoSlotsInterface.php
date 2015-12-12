<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 03/03/2015
 * Time: 12:18
 */

namespace RM\PlantillaBundle\Model\Interfaces;


use RM\PlantillaBundle\Entity\TamanyoImagen;

interface GrupoSlotsInterface
{
    public function setNombre($nombre);


    public function getNombre();


    public function setTipo($tipo);


    public function getTipo();


    public function setEstado($estado);


    public function getEstado();


    public function setMNombreProducto($mNombreProducto);


    public function getMNombreProducto();


    public function setMImgProducto($mImgProducto);


    public function getMImgProducto();


    public function setMPrecio($mPrecio);


    public function getMPrecio();


    public function setMVolumen($mVolumen);


    public function getMVolumen();


    public function setMCondiciones($mCondiciones);


    public function getMCondiciones();


    public function setMTexto($mTexto);


    public function getMTexto();


    public function setMVoucher($mVoucher);


    public function getMVoucher();


    public function setMFidelizacion($mFidelizacion);


    public function getMFidelizacion();


    public function getIdGrupo();


    public function setIdTamanyoSlot(TamanyoImagen $idTamanyoSlot = null);


    public function getIdTamanyoSlot();


    public function setIdPlantilla(PlantillaInterface $idPlantilla = null);


    public function getIdPlantilla();


    public function setNumSlots($numSlots);


    public function getNumSlots();


    public function getSlots();
} 