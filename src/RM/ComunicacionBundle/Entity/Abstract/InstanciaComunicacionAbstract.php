<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @return InstanciaComunicacionAbstract
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
    public abstract function getGenericas();


    /**
     * @param GrupoSlots $grupoSlot
     *
     * @return \Doctrine\Common\Collections\Collection|static
     */
    public abstract function getNumPromocionesByGrupo(GrupoSlots $grupoSlot);


} 