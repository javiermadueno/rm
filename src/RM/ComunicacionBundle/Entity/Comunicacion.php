<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Model\Interfaces\FechaInicioFinInterface;
use RM\ComunicacionBundle\Model\Validator as ComunicacionAssert;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

/**
 * Comunicacion
 *
 * @ORM\Table(name="comunicacion")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\ComunicacionRepository")
 * @ComunicacionAssert\CompruebaFechas()
 */
class Comunicacion implements FechaInicioFinInterface
{
    const ESTADO_CONFIGURACION = 0;
    const ESTADO_ACTIVO        = 1;
    const ESTADO_PAUSADO       = 2;
    const ESTADO_COMPLETADA    = 3;
    const ESTADO_ELIMINADO     = -1;


    /**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
	 */
	private $nombre;

    /**
     * @var \Datetime
     *  
     * @ORM\Column(name="fec_inicio", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecInicio;

    /**
     * @var \Datetime
     * 
     * @ORM\Column(name="fec_fin", type="date", nullable=true)
     * @Assert\Date()
     */
    private $fecFin;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
	 * @var integer
	 *
	 * @ORM\Column(name="id_comunicacion", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
    private $idComunicacion;

    /**
     * @var \RM\ComunicacionBundle\Entity\Canal
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Canal", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_canal", referencedColumnName="id_canal")
     * })
     */
    private $idCanal;

    /**
     * @var bool
     * @ORM\Column(name="generada", type="boolean")
     */
    private $generada;

    /**
     * @var PlantillaInterface
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\Plantilla", inversedBy="comunicaciones")
     * @ORM\JoinColumn(name="id_plantilla", referencedColumnName="id_plantilla")
     */
    private $plantilla;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RM\ComunicacionBundle\Entity\SegmentoComunicacion", mappedBy="idComunicacion")
     */
    private $segmentos;

    public function __construct()
    {
        $this->segmentos = new ArrayCollection();
    }



    private $fecProximaEjecucion;

    public function getFecProximaEjecucion()
    {
        return $this->fecProximaEjecucion;
    }

    public function setFecProximaEjecucion($fecha)
    {
        $this->fecProximaEjecucion =  $fecha;

        return $this;
    }


    
    public function __toString()
    {
    	return $this->getNombre();
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Comunicacion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set fecInicio
     *
     * @param \Datetime $fecInicio
     * @return Comunicacion
     */
    public function setFecInicio($fecInicio = null)
    {
        $this->fecInicio = $fecInicio;
    
        return $this;
    }

    /**
     * Get fecInicio
     *
     * @return \Datetime
     */
    public function getFecInicio()
    {
        return $this->fecInicio;
    }

    /**
     * Set fecFin
     *
     * @param \Datetime $fecFin
     * @return Comunicacion
     */
    public function setFecFin($fecFin = null)
    {
        $this->fecFin = $fecFin;
    
        return $this;
    }

    /**
     * Get fecFin
     *
     * @return \Datetime
     */
    public function getFecFin()
    {
        return $this->fecFin;
    }

    /**
     * Set estado
     *
     * @param int $estado
     * @return Comunicacion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Get idComunicacion
     *
     * @return integer 
     */
    public function getIdComunicacion()
    {
        return $this->idComunicacion;
    }

    /**
     * Set idCanal
     *
     * @param Canal $idCanal
     * @return Comunicacion
     */
    public function setIdCanal(Canal $idCanal = null)
    {
        $this->idCanal = $idCanal;
    
        return $this;
    }

    /**
     * Get idCanal
     *
     * @return \RM\ComunicacionBundle\Entity\Canal 
     */
    public function getIdCanal()
    {
        return $this->idCanal;
    }


    /**
     * Set generada
     *
     * @param boolean $generada
     * @return Comunicacion
     */
    public function setGenerada($generada)
    {
        $this->generada = $generada;
    
        return $this;
    }

    /**
     * Get generada
     *
     * @return boolean 
     */
    public function getGenerada()
    {
        return $this->generada;
    }




    /**
     * Set plantilla
     *
     * @param Plantilla $plantilla
     * @return Comunicacion
     */
    public function setPlantilla(Plantilla $plantilla = null)
    {
        $this->plantilla = $plantilla;
    
        return $this;
    }

    /**
     * Get plantilla
     *
     * @return Plantilla
     */
    public function getPlantilla()
    {
        return $this->plantilla;
    }

    /**
     * Add segmentos
     *
     * @param \RM\ComunicacionBundle\Entity\SegmentoComunicacion $segmentos
     * @return Comunicacion
     */
    public function addSegmento(SegmentoComunicacion $segmentos)
    {
        $this->segmentos[] = $segmentos;
    
        return $this;
    }

    /**
     * Remove segmentos
     *
     * @param SegmentoComunicacion $segmentos
     */
    public function removeSegmento(SegmentoComunicacion $segmentos)
    {
        $this->segmentos->removeElement($segmentos);
    }

    /**
     * Get segmentos
     *
     * @return Collection
     */
    public function getSegmentos()
    {
        return $this->segmentos->filter(function(SegmentoComunicacion $segmento){
            return $segmento->getEstado() > -1;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getGruposSlots()
    {
        return $this->plantilla->getGruposSlots();
    }
}