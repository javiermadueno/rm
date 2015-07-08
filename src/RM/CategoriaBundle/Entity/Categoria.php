<?php

namespace RM\CategoriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria")
 * @ORM\Entity(repositoryClass="RM\CategoriaBundle\Entity\CategoriaRepository")
 */
class Categoria
{
	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
	 */
	private $nombre;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_categoria", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idCategoria;
	
	/**
	 * @var smallint
	 *
	 * @ORM\Column(name="asociado", type="smallint", nullable=true)
	 */
	private $asociado;
	
	/**
	 * @var \NivelCategoria
	 *
	 * @ORM\ManyToOne(targetEntity="NivelCategoria", cascade={"persist", "remove"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_nivel_categoria", referencedColumnName="id_nivel_categoria")
	 * })
	 */
	private $idNivelCategoria;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="estado", type="smallint", nullable=true)
	 */
	private $estado;

	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 * @return Categoria
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
	 * Get idCategoria
	 *
	 * @return integer
	 */
	public function getIdCategoria()
	{
		return $this->idCategoria;
	}

	
	/**
	 * Get asociado
	 *
	 * @return integer
	 */
	public function getAsociado() 
	{
		return $this->asociado;
	}
	
	/**
     * Set asociado
     *
     * @param smallint $asociado
     * @return Categoria
     */
	public function setAsociado( $asociado) 
	{
		$this->asociado = $asociado;
		return $this;
	}
	
	/**
	 * Get Categoria
	 *
	 * @return \RM\CategoriaBundle\Entity\NivelCategoria
	 */
	public function getIdNivelCategoria() 
	{
		return $this->idNivelCategoria;
	}
	
	/**
	 * Set idNivelCategoria
	 *
	 * @param \RM\CategoriaBundle\Entity\NivelCategoria $idNivelCategoria
	 * @return Categoria
	 */
	public function setIdNivelCategoria(\RM\CategoriaBundle\Entity\NivelCategoria $idNivelCategoria = null) 
	{
		$this->idNivelCategoria = $idNivelCategoria;
		return $this;
	}
	
	
	/**
	 * Set estado
	 *
	 * @param string $estado
	 * @return Categoria
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
	
}


