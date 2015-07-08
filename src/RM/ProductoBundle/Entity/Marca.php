<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Marca
 *
 * @ORM\Table(name="marca")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\MarcaRepository")
 */
class Marca
{

  public function __construct(){
    $this->productos = new ArrayCollection();
  }
	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
	 */
	private $nombre;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_marca", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idMarca;

	/**
	 * @var \RM\VariableBundle\Entity\Proveedor
	 *
	 * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Proveedor", cascade={"persist", "remove"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id_proveedor")
	 * })
	 */
	private $idProveedor;

  /**
   * @var \RM\ProductoBundle\Entity\Producto
   *
   * @ORM\OneToMany(targetEntity="RM\ProductoBundle\Entity\Producto", mappedBy="idMarca")
   */
  private $productos;


	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 * @return Marca
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
	 * Get idMarca
	 *
	 * @return integer
	 */
	public function getIdMarca()
	{
		return $this->idMarca;
	}

	/**
	 * Set idProveedor
	 *
	 * @param \RM\ProductoBundle\Entity\Proveedor $idProveedor
	 * @return Marca
	 */
	public function setIdProveedor(\RM\ProductoBundle\Entity\Proveedor $idProveedor = null)
	{
		$this->idProveedor = $idProveedor;

		return $this;
	}

	/**
	 * Get idProveedor
	 *
	 * @return \RM\ProductoBundle\Entity\Proveedor
	 */
	public function getIdProveedor()
	{
		return $this->idProveedor;
	}

  /**
   * @return ArrayCollection|Producto
   */
  public function getProductos(){
    return $this->productos;
  }
}