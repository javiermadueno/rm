<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proveedor
 *
 * @ORM\Table(name="proveedor")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\ProveedorRepository")
 */
class Proveedor
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
	 * @ORM\Column(name="id_proveedor", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idProveedor;



	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 * @return Proveedor
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
	 * Get idProveedor
	 *
	 * @return integer
	 */
	public function getIdProveedor()
	{
		return $this->idProveedor;
	}
}