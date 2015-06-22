<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\ProductoRepository")
 */
class Producto implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_sku", type="string", length=255, nullable=true)
     */
    private $codSku;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_lanzamiento", type="datetime", nullable=true)
     */
    private $fechaLanzamiento;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_venta", type="float", nullable=true)
     */
    private $precioVenta;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_estandar", type="float", nullable=true)
     */
    private $precioEstandar;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_unitario", type="float", nullable=true)
     */
    private $precioUnitario;

    /**
     * @var string
     *
     * @ORM\Column(name="volumen", type="string", length=255, nullable=true)
     */
    private $volumen;

    /**
     * @var smallint
     *
     * @ORM\Column(name="activo", type="smallint", nullable=true)
     */
    private $activo;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria3", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria3;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria4", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria4;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria5", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria5;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria6", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria6;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria7", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria7;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria8", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria8;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria9", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria9;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria10", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria10;


    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria11", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria11;

    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria2", referencedColumnName="id_categoria", nullable=true)
     * })
     */
    private $idCategoria2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_producto", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProducto;

    /**
     * @var \RM\ProductoBundle\Entity\Marca
     *
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Marca", inversedBy="productos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_marca", referencedColumnName="id_marca")
     * })
     */
    private $idMarca;

    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id_categoria")
     * })
     */
    private $idCategoria;

    /**
     * @var \RM\ProductoBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Proveedor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id_proveedor")
     * })
     */
    private $idProveedor;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get codSku
     *
     * @return string
     */
    public function getCodSku()
    {
        return $this->codSku;
    }

    /**
     * Set codSku
     *
     * @param string $codSku
     *
     * @return Producto
     */
    public function setCodSku($codSku)
    {
        $this->codSku = $codSku;

        return $this;
    }

    /**
     * Get fechaLanzamiento
     *
     * @return \Date
     */
    public function getFechaLanzamiento()
    {
        return $this->fechaLanzamiento;
    }

    /**
     * Set fechaLanzamiento
     *
     * @param \Date $fechaLanzamiento
     *
     * @return Producto
     */
    public function setFechaLanzamiento($fechaLanzamiento)
    {
        $this->fechaLanzamiento = $fechaLanzamiento;

        return $this;
    }

    /**
     * Get precioVenta
     *
     * @return float
     */
    public function getPrecioVenta()
    {
        return (float)$this->precioVenta;
    }

    /**
     * Set precioVenta
     *
     * @param float $precioVenta
     *
     * @return Producto
     */
    public function setPrecioVenta($precioVenta)
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    /**
     * Get precioEstandar
     *
     * @return float
     */
    public function getPrecioEstandar()
    {
        return $this->precioEstandar;
    }

    /**
     * Set precioEstandar
     *
     * @param float $precioEstandar
     *
     * @return Producto
     */
    public function setPrecioEstandar($precioEstandar)
    {
        $this->precioEstandar = $precioEstandar;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return float
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /**
     * Set precioUnitario
     *
     * @param float $precioUnitario
     *
     * @return Producto
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get volumen
     *
     * @return string
     */
    public function getVolumen()
    {
        return $this->volumen;
    }

    /**
     * Set volumen
     *
     * @param string $volumen
     *
     * @return Producto
     */
    public function setVolumen($volumen)
    {
        $this->volumen = $volumen;

        return $this;
    }

    /**
     * Get activo
     *
     * @return smallint
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set activo
     *
     * @param smallint $activo
     *
     * @return Producto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get idProducto
     *
     * @return integer
     */
    public function getIdProducto()
    {
        return $this->idProducto;
    }

    /**
     * Get idMarca
     *
     * @return \RM\ProductoBundle\Entity\Marca
     */
    public function getIdMarca()
    {
        return $this->idMarca;
    }

    /**
     * Set idMarca
     *
     * @param \RM\ProductoBundle\Entity\Marca $idMarca
     *
     * @return Producto
     */
    public function setIdMarca(\RM\ProductoBundle\Entity\Marca $idMarca = null)
    {
        $this->idMarca = $idMarca;

        return $this;
    }

    /**
     * Get idCategoria
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /**
     * Set idCategoria
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria
     *
     * @return Producto
     */
    public function setIdCategoria(\RM\CategoriaBundle\Entity\Categoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;

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
     * Set idProveedor
     *
     * @param \RM\ProductoBundle\Entity\Proveedor $idProveedor
     *
     * @return Producto
     */
    public function setIdProveedor(\RM\ProductoBundle\Entity\Proveedor $idProveedor = null)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    public function jsonSerialize()
    {

        return [
            'id'     => $this->idProducto,
            'nombre' => $this->nombre
        ];
    }

    /**
     * Get idCategoria3
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria3()
    {
        return $this->idCategoria3;
    }

    /**
     * Set idCategoria3
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria3
     *
     * @return Producto
     */
    public function setIdCategoria3(\RM\CategoriaBundle\Entity\Categoria $idCategoria3 = null)
    {
        $this->idCategoria3 = $idCategoria3;

        return $this;
    }

    /**
     * Get idCategoria4
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria4()
    {
        return $this->idCategoria4;
    }

    /**
     * Set idCategoria4
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria4
     *
     * @return Producto
     */
    public function setIdCategoria4(\RM\CategoriaBundle\Entity\Categoria $idCategoria4 = null)
    {
        $this->idCategoria4 = $idCategoria4;

        return $this;
    }

    /**
     * Get idCategoria5
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria5()
    {
        return $this->idCategoria5;
    }

    /**
     * Set idCategoria5
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria5
     *
     * @return Producto
     */
    public function setIdCategoria5(\RM\CategoriaBundle\Entity\Categoria $idCategoria5 = null)
    {
        $this->idCategoria5 = $idCategoria5;

        return $this;
    }

    /**
     * Get idCategoria6
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria6()
    {
        return $this->idCategoria6;
    }

    /**
     * Set idCategoria6
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria6
     *
     * @return Producto
     */
    public function setIdCategoria6(\RM\CategoriaBundle\Entity\Categoria $idCategoria6 = null)
    {
        $this->idCategoria6 = $idCategoria6;

        return $this;
    }

    /**
     * Get idCategoria7
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria7()
    {
        return $this->idCategoria7;
    }

    /**
     * Set idCategoria7
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria7
     *
     * @return Producto
     */
    public function setIdCategoria7(\RM\CategoriaBundle\Entity\Categoria $idCategoria7 = null)
    {
        $this->idCategoria7 = $idCategoria7;

        return $this;
    }

    /**
     * Get idCategoria8
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria8()
    {
        return $this->idCategoria8;
    }

    /**
     * Set idCategoria8
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria8
     *
     * @return Producto
     */
    public function setIdCategoria8(\RM\CategoriaBundle\Entity\Categoria $idCategoria8 = null)
    {
        $this->idCategoria8 = $idCategoria8;

        return $this;
    }

    /**
     * Get idCategoria9
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria9()
    {
        return $this->idCategoria9;
    }

    /**
     * Set idCategoria9
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria9
     *
     * @return Producto
     */
    public function setIdCategoria9(\RM\CategoriaBundle\Entity\Categoria $idCategoria9 = null)
    {
        $this->idCategoria9 = $idCategoria9;

        return $this;
    }

    /**
     * Get idCategoria10
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria10()
    {
        return $this->idCategoria10;
    }

    /**
     * Set idCategoria10
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria10
     *
     * @return Producto
     */
    public function setIdCategoria10(\RM\CategoriaBundle\Entity\Categoria $idCategoria10 = null)
    {
        $this->idCategoria10 = $idCategoria10;

        return $this;
    }

    /**
     * Get idCategoria11
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria11()
    {
        return $this->idCategoria11;
    }

    /**
     * Set idCategoria11
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria11
     *
     * @return Producto
     */
    public function setIdCategoria11(\RM\CategoriaBundle\Entity\Categoria $idCategoria11 = null)
    {
        $this->idCategoria11 = $idCategoria11;

        return $this;
    }

    /**
     * Get idCategoria2
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria2()
    {
        return $this->idCategoria2;
    }

    /**
     * Set idCategoria2
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria2
     *
     * @return Producto
     */
    public function setIdCategoria2(\RM\CategoriaBundle\Entity\Categoria $idCategoria2 = null)
    {
        $this->idCategoria2 = $idCategoria2;

        return $this;
    }
}