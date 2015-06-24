<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\ProductoBundle\Entity\NumPromociones;

/**
 * InstanciaComunicacion
 *
 * @ORM\Table(name="instancia_comunicacion")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository")
 */
class InstanciaComunicacion
{
    const FASE_CONFIGURACION = 'cfg';
    const FASE_NEGOCIACION = 'ngc';
    const FASE_SIMULACION = 'sim';
    const FASE_CIERRE = 'cie';
    const FASE_GENERACION = 'gen';
    const FASE_CONFIRMACION = 'cnf';
    const FASE_FINALIZADA = 'fin';
    const FASE_CANCELADA = 'can';
    const PASO_1 = 1;
    const PASO_2 = 2;

    public function __construct()
    {
        $this->numPromociones = new ArrayCollection();
    }

    /**
     * @var \Date
     *
     * @ORM\Column(name="fec_creacion", type="datetime", nullable=true)
     */
    private $fecCreacion;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fec_ejecucion", type="datetime", nullable=true)
     */
    private $fecEjecucion;

    /**
     * @var \RM\ComunicacionBundle\Entity\Fases
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Fases")
     * @ORM\JoinColumn(name="fase", referencedColumnName="id_fase")
     */
    private $fase;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_instancia", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idInstancia;

    /**
     * @var \RM\ComunicacionBundle\Entity\SegmentoComunicacion
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\SegmentoComunicacion")
     * @ORM\JoinColumn(name="id_segmento_comunicacion", referencedColumnName="id_segmento_comunicacion")
     *
     */
    private $idSegmentoComunicacion;

  /**
   * @var ArrayCollection
   * @ORM\OneToMany(targetEntity="RM\ProductoBundle\Entity\NumPromociones", mappedBy="idInstancia")
   */
    private $numPromociones;

    /**
     * @var integer
     * @ORM\Column(name="paso", type="integer", nullable=false)
     */
    private $paso;



    /**
     * Set fecCreacion
     *
     * @param \Date $fecCreacion
     * @return InstanciaComunicacion
     */
    public function setFecCreacion($fecCreacion)
    {
        $this->fecCreacion = $fecCreacion;
    
        return $this;
    }

    /**
     * Get fecCreacion
     *
     * @return \Date 
     */
    public function getFecCreacion()
    {
        return $this->fecCreacion;
    }

    /**
     * Set fecEjecucion
     *
     * @param \Date $fecEjecucion
     * @return InstanciaComunicacion
     */
    public function setFecEjecucion($fecEjecucion)
    {
        $this->fecEjecucion = $fecEjecucion;
    
        return $this;
    }

    /**
     * Get fecEjecucion
     *
     * @return \Date 
     */
    public function getFecEjecucion()
    {
        return $this->fecEjecucion;
    }

    /**
     * Set fase
     *
     * @param \RM\ComunicacionBundle\Entity\Fases $fase
     * @return InstanciaComunicacion
     */
    public function setFase($fase)
    {
        $this->fase = $fase;
    
        return $this;
    }

    /**
     * Get fase
     *
     * @return \RM\ComunicacionBundle\Entity\Fases
     */
    public function getFase()
    {
        return $this->fase;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
     * @return InstanciaComunicacion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return smallint 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Get idInstancia
     *
     * @return integer 
     */
    public function getIdInstancia()
    {
        return $this->idInstancia;
    }

    /**
     * Set idSegmentoComunicacion
     *
     * @param \RM\ComunicacionBundle\Entity\SegmentoComunicacion $idSegmentoComunicacion
     * @return InstanciaComunicacion
     */
    public function setIdSegmentoComunicacion(\RM\ComunicacionBundle\Entity\SegmentoComunicacion $idSegmentoComunicacion = null)
    {
        $this->idSegmentoComunicacion = $idSegmentoComunicacion;
    
        return $this;
    }

    /**
     * Get idSegmentoComunicacion
     *
     * @return \RM\ComunicacionBundle\Entity\SegmentoComunicacion
     */
    public function getIdSegmentoComunicacion()
    {
        return $this->idSegmentoComunicacion;
    }

    /**
     * Add numPromociones
     *
     * @param \RM\ProductoBundle\Entity\NumPromociones $numPromociones
     * @return InstanciaComunicacion
     */
    public function addNumPromocion(\RM\ProductoBundle\Entity\NumPromociones $numPromociones)
    {
        $this->numPromociones[] = $numPromociones;
    
        return $this;
    }

    /**
     * Remove numPromociones
     *
     * @param \RM\ProductoBundle\Entity\NumPromociones $numPromociones
     */
    public function removeNumPromocion(\RM\ProductoBundle\Entity\NumPromociones $numPromociones)
    {
        $this->numPromociones->removeElement($numPromociones);
    }

    /**
     * Get numPromociones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNumPromociones()
    {
        return $this->numPromociones->filter(function(NumPromociones $numPro){
                return $numPro->getEstado() > -1;
            });
    }


    /**
     * Set paso
     *
     * @param integer $paso
     * @return InstanciaComunicacion
     */
    public function setPaso($paso)
    {
        $this->paso = $paso;
    
        return $this;
    }

    /**
     * Get paso
     *
     * @return integer 
     */
    public function getPaso()
    {
        return $this->paso;
    }

    /**
     * Add numPromociones
     *
     * @param \RM\ProductoBundle\Entity\NumPromociones $numPromociones
     * @return InstanciaComunicacion
     */
    public function addNumPromocione(\RM\ProductoBundle\Entity\NumPromociones $numPromociones)
    {
        $this->numPromociones[] = $numPromociones;
    
        return $this;
    }

    /**
     * Remove numPromociones
     *
     * @param \RM\ProductoBundle\Entity\NumPromociones $numPromociones
     */
    public function removeNumPromocione(\RM\ProductoBundle\Entity\NumPromociones $numPromociones)
    {
        $this->numPromociones->removeElement($numPromociones);
    }
}