<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Model\Interfaces\FechaInicioFinInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use RM\ComunicacionBundle\Model\Validator as ComunicacionAssert;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var \Date
     *  
     * @ORM\Column(name="fec_inicio", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $fecInicio;

    /**
     * @var \Date
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
     * @param \Date $fecInicio
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
     * @return \Date 
     */
    public function getFecInicio()
    {
        return $this->fecInicio;
    }

    /**
     * Set fecFin
     *
     * @param \Date $fecFin
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
     * @return \Date 
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
     * @param \RM\ComunicacionBundle\Entity\Canal $idCanal
     * @return Comunicacion
     */
    public function setIdCanal(\RM\ComunicacionBundle\Entity\Canal $idCanal = null)
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
     * @param \RM\PlantillaBundle\Entity\Plantilla $plantilla
     * @return Comunicacion
     */
    public function setPlantilla(\RM\PlantillaBundle\Entity\Plantilla $plantilla = null)
    {
        $this->plantilla = $plantilla;
    
        return $this;
    }

    /**
     * Get plantilla
     *
     * @return \RM\PlantillaBundle\Entity\Plantilla 
     */
    public function getPlantilla()
    {
        return $this->plantilla;
    }
}