<?php

namespace RM\ProcesosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Procesos
 *
 * @ORM\Table(name="procesos")
 * @ORM\Entity(repositoryClass="RM\ProcesosBundle\Entity\ProcesoRepository")
 */
class Proceso
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_proceso", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="datetime")
     */
    private $fechaCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="datetime")
     */
    private $fechaFin;

    /**
     * @var string
     *
     * @ORM\Column(name="id_centro", type="string", length=255)
     */
    private $idCentro;

    /**
     * @var string
     *
     * @ORM\Column(name="uid_usuario", type="string", length=255)
     */
    private $uidUsuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_estado_proceso", type="integer")
     * @ORM\OneToOne(targetEntity="RM\ProcesosBundle\Entity\EstadoProceso")
     */
    private $estadoProceso;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_proceso", type="integer")
     * @ORM\OneToOne(targetEntity="RM\ProcesosBundle\Entity\TipoProceso")
     */
    private $tipoProceso;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $tipoProceso
     * @return $this
     */
    public function setTipoProceso($tipoProceso)
    {
        $this->tipoProceso = $tipoProceso;

        return $this;
    }

    /**
     * Get idTipoProceso
     *
     * @return \RM\ProcesosBundle\Entity\TipoProceso
     */
    public function getTipoProceso()
    {
        return $this->tipoProceso;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return Proceso
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
    
        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Proceso
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    
        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return Proceso
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    
        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime 
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set idCentro
     *
     * @param string $idCentro
     * @return Proceso
     */
    public function setIdCentro($idCentro)
    {
        $this->idCentro = $idCentro;
    
        return $this;
    }

    /**
     * Get idCentro
     *
     * @return string 
     */
    public function getIdCentro()
    {
        return $this->idCentro;
    }

    /**
     * Set uidUsuario
     *
     * @param string $uidUsuario
     * @return Proceso
     */
    public function setUidUsuario($uidUsuario)
    {
        $this->uidUsuario = $uidUsuario;
    
        return $this;
    }

    /**
     * Get uidUsuario
     *
     * @return string 
     */
    public function getUidUsuario()
    {
        return $this->uidUsuario;
    }

    /**
     * Set estadoProceso
     *
     * @param \RM\ProcesosBundle\Entity\TipoProceso
     * @return Proceso
     */
    public function setEstadoProceso($estadoProceso)
    {
        $this->estadoProceso = $estadoProceso;
    
        return $this;
    }

    /**
     * Get estadoProceso
     *
     * @return \RM\ProcesosBundle\Entity\TipoProceso
     */
    public function getEstadoProceso()
    {
        return $this->estadoProceso;
    }
}
