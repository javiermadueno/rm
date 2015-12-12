<?php

namespace RM\ComunicacionBundle\Model\Abstracts;

use Doctrine\Common\Collections\ArrayCollection;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;

/**
 * Created by PhpStorm.
 * User: javi
 * Date: 29/08/15
 * Time: 16:04
 */
abstract class InstanciaComunicacionAbstract
{


    /**
     * Get fecCreacion
     *
     * @return \Datetime
     */
    public abstract function getFecCreacion();


    /**
     * Get fecEjecucion
     *
     * @return \Datetime
     */
    public abstract function getFecEjecucion();


    /**
     * Get fase
     *
     * @return \RM\ComunicacionBundle\Entity\Fases
     */
    public abstract function getFase();


    /**
     * Get estado
     *
     * @return int
     */
    public abstract function getEstado();

    /**
     * Get idInstancia
     *
     * @return integer
     */
    public abstract function getIdInstancia();



    /**
     * Get idSegmentoComunicacion
     *
     * @return SegmentoComunicacion
     */
    public abstract function getIdSegmentoComunicacion();



    /**
     * Get numPromociones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public abstract function getNumPromociones();



    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public abstract function getGruposSlots();


    /**
     * @return ArrayCollection
     */
    public abstract function getPromocionesSegmentadas();


    /**
     * @return ArrayCollection
     */
    public abstract function getPromocionesGenericas();


    /**
     * @param GrupoSlots $grupoSlot
     *
     * @return \Doctrine\Common\Collections\Collection|static
     */
    public abstract function getNumPromocionesByGrupo(GrupoSlots $grupoSlot);


    /**
     * @return mixed
     */
    public abstract function getTotalPromocionesAceptadas();


    /**
     * @return number
     */
    public abstract function getTotalPromocionesRechazadas();


    /**
     * @return number
     */
    public abstract function getTotalPromocionesPendientes();

    /**
     * @return string
     */
    public function getNombreComunicacion()
    {
        return $this->getIdSegmentoComunicacion()
            ->getIdComunicacion()
            ->getNombre();
    }

    public function getNombreSegmento()
    {
       return  $this->getIdSegmentoComunicacion()
            ->getIdSegmento()
            ->getNombre();
    }


} 