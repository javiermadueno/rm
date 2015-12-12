<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use RM\CategoriaBundle\Entity\Categoria;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\ProductoRepository")
 */
class Producto implements  JsonSerializable
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
     * @var \DateTime
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
     * @var int
     *
     * @ORM\Column(name="activo", type="smallint", nullable=true)
     */
    private $activo;

    /**
     * @var string
     */
    private $url;



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
     * @var string
     * @ORM\Column(name="imagen", type="string")
     */
    private $imagen;

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    public function uploadImagen($cliente_path = '')
    {
        if(is_null($this->file) || !$this->file->isValid()) {
            return;
        }

        if (empty($cliente_path)) {
            throw new \Exception("No se ha definido el cliente");
        }


        $this->removeImagen($cliente_path);

        $nombre_imagen  =  $this->idProducto . '.' . $this->getFile()->getClientOriginalExtension();
        $this->getFile()->move(
            $cliente_path,
            $nombre_imagen
        );

        // set the path property to the filename where you've saved the file

        $this->setImagen($nombre_imagen);

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }



    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Producto
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
     * Set codSku
     *
     * @param string $codSku
     * @return Producto
     */
    public function setCodSku($codSku)
    {
        $this->codSku = $codSku;
    
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
     * Set fechaLanzamiento
     *
     * @param \DateTime $fechaLanzamiento
     * @return Producto
     */
    public function setFechaLanzamiento($fechaLanzamiento)
    {
        $this->fechaLanzamiento = $fechaLanzamiento;
    
        return $this;
    }

    /**
     * Get fechaLanzamiento
     *
     * @return \DateTime
     */
    public function getFechaLanzamiento()
    {
        return $this->fechaLanzamiento;
    }

    /**
     * Set precioVenta
     *
     * @param float $precioVenta
     * @return Producto
     */
    public function setPrecioVenta($precioVenta)
    {
        $this->precioVenta = $precioVenta;
    
        return $this;
    }

    /**
     * Get precioVenta
     *
     * @return float 
     */
    public function getPrecioVenta()
    {
        return (float) $this->precioVenta;
    }

    /**
     * Set precioEstandar
     *
     * @param float $precioEstandar
     * @return Producto
     */
    public function setPrecioEstandar($precioEstandar)
    {
        $this->precioEstandar = $precioEstandar;
    
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
     * Set precioUnitario
     *
     * @param float $precioUnitario
     * @return Producto
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;
    
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
     * Set volumen
     *
     * @param string $volumen
     * @return Producto
     */
    public function setVolumen($volumen)
    {
        $this->volumen = $volumen;
    
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
     * Set activo
     *
     * @param int $activo
     * @return Producto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    
        return $this;
    }

    /**
     * Get activo
     *
     * @return int
     */
    public function getActivo()
    {
        return $this->activo;
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
     * Set idMarca
     *
     * @param Marca $idMarca
     * @return Producto
     */
    public function setIdMarca(Marca $idMarca = null)
    {
        $this->idMarca = $idMarca;
    
        return $this;
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
     * Set idCategoria
     *
     * @param Categoria $idCategoria
     * @return Producto
     */
    public function setIdCategoria(Categoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;
    
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
     * Set idProveedor
     *
     * @param Proveedor $idProveedor
     * @return Producto
     */
    public function setIdProveedor(Proveedor $idProveedor = null)
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

    public function jsonSerialize(){

        return [
            'id' => $this->idProducto,
            'nombre' => $this->nombre
        ];
    }

    /**
     * Set idCategoria3
     *
     * @param Categoria $idCategoria3
     * @return Producto
     */
    public function setIdCategoria3(Categoria $idCategoria3 = null)
    {
        $this->idCategoria3 = $idCategoria3;
    
        return $this;
    }

    /**
     * Get idCategoria3
     *
     * @return Categoria
     */
    public function getIdCategoria3()
    {
        return $this->idCategoria3;
    }

    /**
     * Set idCategoria4
     *
     * @param Categoria $idCategoria4
     * @return Producto
     */
    public function setIdCategoria4(Categoria $idCategoria4 = null)
    {
        $this->idCategoria4 = $idCategoria4;
    
        return $this;
    }

    /**
     * Get idCategoria4
     *
     * @return Categoria
     */
    public function getIdCategoria4()
    {
        return $this->idCategoria4;
    }

    /**
     * Set idCategoria5
     *
     * @param Categoria $idCategoria5
     * @return Producto
     */
    public function setIdCategoria5(Categoria $idCategoria5 = null)
    {
        $this->idCategoria5 = $idCategoria5;
    
        return $this;
    }

    /**
     * Get idCategoria5
     *
     * @return Categoria
     */
    public function getIdCategoria5()
    {
        return $this->idCategoria5;
    }

    /**
     * Set idCategoria6
     *
     * @param Categoria $idCategoria6
     * @return Producto
     */
    public function setIdCategoria6(Categoria $idCategoria6 = null)
    {
        $this->idCategoria6 = $idCategoria6;
    
        return $this;
    }

    /**
     * Get idCategoria6
     *
     * @return Categoria
     */
    public function getIdCategoria6()
    {
        return $this->idCategoria6;
    }

    /**
     * Set idCategoria7
     *
     * @param Categoria $idCategoria7
     * @return Producto
     */
    public function setIdCategoria7(Categoria $idCategoria7 = null)
    {
        $this->idCategoria7 = $idCategoria7;
    
        return $this;
    }

    /**
     * Get idCategoria7
     *
     * @return Categoria
     */
    public function getIdCategoria7()
    {
        return $this->idCategoria7;
    }

    /**
     * Set idCategoria8
     *
     * @param Categoria $idCategoria8
     * @return Producto
     */
    public function setIdCategoria8(Categoria $idCategoria8 = null)
    {
        $this->idCategoria8 = $idCategoria8;
    
        return $this;
    }

    /**
     * Get idCategoria8
     *
     * @return Categoria
     */
    public function getIdCategoria8()
    {
        return $this->idCategoria8;
    }

    /**
     * Set idCategoria9
     *
     * @param Categoria $idCategoria9
     * @return Producto
     */
    public function setIdCategoria9(Categoria $idCategoria9 = null)
    {
        $this->idCategoria9 = $idCategoria9;
    
        return $this;
    }

    /**
     * Get idCategoria9
     *
     * @return Categoria
     */
    public function getIdCategoria9()
    {
        return $this->idCategoria9;
    }

    /**
     * Set idCategoria10
     *
     * @param Categoria $idCategoria10
     * @return Producto
     */
    public function setIdCategoria10(Categoria $idCategoria10 = null)
    {
        $this->idCategoria10 = $idCategoria10;
    
        return $this;
    }

    /**
     * Get idCategoria10
     *
     * @return Categoria
     */
    public function getIdCategoria10()
    {
        return $this->idCategoria10;
    }

    /**
     * Set idCategoria11
     *
     * @param Categoria $idCategoria11
     * @return Producto
     */
    public function setIdCategoria11(Categoria $idCategoria11 = null)
    {
        $this->idCategoria11 = $idCategoria11;
    
        return $this;
    }

    /**
     * Get idCategoria11
     *
     * @return Categoria
     */
    public function getIdCategoria11()
    {
        return $this->idCategoria11;
    }

    /**
     * Set idCategoria2
     *
     * @param Categoria $idCategoria2
     * @return Producto
     */
    public function setIdCategoria2(Categoria $idCategoria2 = null)
    {
        $this->idCategoria2 = $idCategoria2;
    
        return $this;
    }

    /**
     * Get idCategoria2
     *
     * @return Categoria
     */
    public function getIdCategoria2()
    {
        return $this->idCategoria2;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Producto
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    
        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * @param $cliente
     */
    public function removeImagen($cliente)
    {
        if(!$this->imagen) {
            return;
        }

        $imagen  = $cliente . $this->imagen;

        if(file_exists($imagen)) {
            unlink($imagen);
        }

    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


}