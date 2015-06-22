<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuracion
 *
 * @ORM\Table(name="configuracion")
 * @ORM\Entity(repositoryClass="RM\DiscretasBundle\Entity\ConfiguracionRepository")
 */
class Configuracion {

    const GENERAL = 'general';
    const SEGMENTOS = 'segmentos';

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var string
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
	 */
	private $nombre;
	
	/**
	 *
	 * @var string
     * @ORM\Column(name="valor", type="string", length=255, nullable=true)
	 */
	private $valor;
	
	/**
	 *
	 * @var int
     * @ORM\Column(name="estado", type="smallint", nullable=true)
	 */
	private $estado;

    /**
     * @var string
     * @ORM\Column(name="descripcion", type="string")
     */
    private $descripcion;

    /**
     * @var string
     * @ORM\Column(name="tipo", type="string", nullable=false, options={"default": "general"})
     */
    private $tipo;
	
	/**
	 * Set nombre
	 *
	 * @param string $nombre        	
	 * @return Configuracion
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
		
		return $this;
	}
	
	/**
	 * Get nombre
	 *
	 * @return string
	 */
	public function getNombre() {
		return $this->nombre;
	}
	
	/**
	 * Set valor
	 *
	 * @param string $valor      	
	 * @return Configuracion
	 */
	public function setValor($valor) {
		$this->valor = $valor;
		
		return $this;
	}
	
	/**
	 * Get valor
	 *
	 * @return string
	 */
	public function getValor() {
		return $this->valor;
	}
	
	/**
	 * Set estado
	 *
	 * @param smallint $estado        	
	 * @return Configuracion
	 */
	public function setEstado($estado) {
		$this->estado = $estado;
		
		return $this;
	}
	
	/**
	 * Get estado
	 *
	 * @return string
	 */
	public function getEstado() {
		return $this->estado;
	}

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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Configuracion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Configuracion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}