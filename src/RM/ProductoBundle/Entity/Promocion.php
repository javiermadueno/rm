<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use RM\ComunicacionBundle\Entity\Creatividad;



/**
 * Promocion
 *
 * @ORM\Table(name="promocion")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\PromocionRepository")
 */
class Promocion
{
    const TIPO_SEGMENTADA = 0;
    const TIPO_GENERICA = 1;
    const ACEPTADA = 2;
    const RECHAZADA = 3;
    const PENDIENTE = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="imp_consumidor", type="float", nullable=true)
     */
    private $impConsumidor;

    /**
     * @var float
     *
     * @ORM\Column(name="imp_distribuidor", type="float", nullable=true)
     */
    private $impDistribuidor;

    /**
     * @var float
     *
     * @ORM\Column(name="imp_fijo", type="float", nullable=true)
     */
    private $impFijo;

    /**
     * @var string
     *
     * @ORM\Column(name="condiciones", type="string", length=512, nullable=true)
     */
    private $condiciones;

    /**
     * @var string
     *
     * @ORM\Column(name="fidelizacion", type="string", length=512, nullable=true)
     */
    private $fidelizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @var integer
     *
     * @ORM\Column(name="poblacion", type="integer", nullable=true)
     */
    private $poblacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="minimo", type="integer", nullable=true)
     */
    private $minimo;

    /**
     * @var integer
     *
     * @ORM\Column(name="simulado", type="integer", nullable=true)
     */
    private $simulado;

    /**
     * @var string
     *
     * @ORM\Column(name="voucher", type="string", length=512, nullable=true)
     */
    private $voucher;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_promocion", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPromocion;

    /**
     * @var Producto
     *
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_producto", referencedColumnName="id_producto")
     * })
     */
    private $idProducto;


    /**
     * @var TipoPromocion
     *
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\TipoPromocion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_promocion", referencedColumnName="id_tipo_promocion", nullable=true)
     * })
     */
    private $idTipoPromocion;

    /**
     * @var NumPromociones
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\NumPromociones", inversedBy="promociones")
     * @ORM\JoinColumn(name="id_num_pro", referencedColumnName="id_num_pro", nullable=false)
     */
    private $numPromocion;

    /**
     * @var int
     * @ORM\Column(name="tipo", type="smallint",length=6, nullable=false)
     */
    private $tipo;

    /**
     * @var string
     * @ORM\Column(name="filtro_segmentos", type="string", nullable=true)
     */
    private $filtro;

    /**
     * @var string
     * @ORM\Column(name="nombre_filtro", type="string", nullable=true)
     */
    private $nombreFiltro;

    /**
     * @var int
     *
     * @ORM\Column(name="aceptada", type="integer", nullable=false, options={"default" = 1})
     */
    private $aceptada;

    /**
     * @var Creatividad
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Creatividad")
     * @ORM\JoinColumn(name="id_creatividad", referencedColumnName="id_creatividad", nullable=true)
     */
    private $creatividad;

    /**
     * @var \Datetime $fechaCaducidad
     * @ORM\Column(name="fecha_caducidad", type="datetime", nullable=false)
     */
    private $fechaCaducidad;

    /**
     * @return \Datetime
     */
    public function getFechaCaducidad()
    {
        return $this->fechaCaducidad;
    }

    /**
     * @param \Datetime $fechaCaducidad
     */
    public function setFechaCaducidad($fechaCaducidad)
    {
        $this->fechaCaducidad = $fechaCaducidad;
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Promocion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get impConsumidor
     *
     * @return float
     */
    public function getImpConsumidor()
    {
        return $this->impConsumidor;
    }

    /**
     * Set impConsumidor
     *
     * @param float $impConsumidor
     *
     * @return Promocion
     */
    public function setImpConsumidor($impConsumidor)
    {
        $this->impConsumidor = $impConsumidor;

        return $this;
    }

    /**
     * Get impDistribuidor
     *
     * @return float
     */
    public function getImpDistribuidor()
    {
        return $this->impDistribuidor;
    }

    /**
     * Set impDistribuidor
     *
     * @param float $impDistribuidor
     *
     * @return Promocion
     */
    public function setImpDistribuidor($impDistribuidor)
    {
        $this->impDistribuidor = $impDistribuidor;

        return $this;
    }

    /**
     * Get impFijo
     *
     * @return float
     */
    public function getImpFijo()
    {
        return $this->impFijo;
    }

    /**
     * Set impFijo
     *
     * @param float $impFijo
     *
     * @return Promocion
     */
    public function setImpFijo($impFijo)
    {
        $this->impFijo = $impFijo;

        return $this;
    }

    /**
     * Get condiciones
     *
     * @return string
     */
    public function getCondiciones()
    {
        return $this->condiciones;
    }

    /**
     * Set condiciones
     *
     * @param string $condiciones
     *
     * @return Promocion
     */
    public function setCondiciones($condiciones)
    {
        $this->condiciones = $condiciones;

        return $this;
    }

    /**
     * Get fidelizacion
     *
     * @return string
     */
    public function getFidelizacion()
    {
        return $this->fidelizacion;
    }

    /**
     * Set fidelizacion
     *
     * @param string $fidelizacion
     *
     * @return Promocion
     */
    public function setFidelizacion($fidelizacion)
    {
        $this->fidelizacion = $fidelizacion;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Promocion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get poblacion
     *
     * @return integer
     */
    public function getPoblacion()
    {
        return $this->poblacion;
    }

    /**
     * Set poblacion
     *
     * @param integer $poblacion
     *
     * @return Promocion
     */
    public function setPoblacion($poblacion)
    {
        $this->poblacion = $poblacion;

        return $this;
    }

    /**
     * Get minimo
     *
     * @return integer
     */
    public function getMinimo()
    {
        return $this->minimo;
    }

    /**
     * Set minimo
     *
     * @param integer $minimo
     *
     * @return Promocion
     */
    public function setMinimo($minimo)
    {
        $this->minimo = $minimo;

        return $this;
    }

    /**
     * Get simulado
     *
     * @return integer
     */
    public function getSimulado()
    {
        return $this->simulado;
    }

    /**
     * Set simulado
     *
     * @param integer $simulado
     *
     * @return Promocion
     */
    public function setSimulado($simulado)
    {
        $this->simulado = $simulado;

        return $this;
    }

    /**
     * Get voucher
     *
     * @return string
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * Set voucher
     *
     * @param string $voucher
     *
     * @return Promocion
     */
    public function setVoucher($voucher)
    {
        $this->voucher = $voucher;

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
     * Set estado
     *
     * @param int $estado
     *
     * @return Promocion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get idPromocion
     *
     * @return integer
     */
    public function getIdPromocion()
    {
        return $this->idPromocion;
    }

    /**
     * Get idProducto
     *
     * @return Producto
     */
    public function getIdProducto()
    {
        return $this->idProducto;
    }

    /**
     * Set idProducto
     *
     * @param Producto $idProducto
     *
     * @return Promocion
     */
    public function setIdProducto(Producto $idProducto = null)
    {
        $this->idProducto = $idProducto;

        return $this;
    }

    /**
     * Get idTipoPromocion
     *
     * @return \RM\ProductoBundle\Entity\TipoPromocion
     */
    public function getIdTipoPromocion()
    {
        return $this->idTipoPromocion;
    }

    /**
     * Set tipoPromocion
     *
     * @param TipoPromocion $idTipoPromocion
     *
     * @return Promocion
     */
    public function setIdTipoPromocion(TipoPromocion $idTipoPromocion = null)
    {
        $this->idTipoPromocion = $idTipoPromocion;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipo
     *
     * @param $tipo
     *
     * @return Promocion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * get filtro
     *
     * @return string
     */
    public function getFiltro()
    {
        return $this->filtro;
    }

    /**
     * set filtro
     *
     * @param $filtro
     *
     * @return Promocion
     */
    public function setFiltro($filtro)
    {
        $this->filtro = $filtro;

        return $this;
    }

    /**
     * Get aceptada
     *
     * @return int
     */
    public function getAceptada()
    {
        return $this->aceptada;
    }

    /**
     * Set aceptada
     *
     * @param int $aceptada
     *
     * @return Promocion
     */
    public function setAceptada($aceptada)
    {
        $this->aceptada = $aceptada;

        return $this;
    }

    /**
     * Get numPromocion
     *
     * @return NumPromociones
     */
    public function getNumPromocion()
    {
        return $this->numPromocion;
    }

    /**
     * Set numPromocion
     *
     * @param NumPromociones $numPromocion
     *
     * @return Promocion
     */
    public function setNumPromocion(NumPromociones $numPromocion = null)
    {
        $this->numPromocion = $numPromocion;

        return $this;
    }


    /**
     * @param $nivel
     *
     * @return array
     */
    public function getProductosByMarca($nivel)
    {
        if ($this->idProducto) {

            $productos   = $this->idProducto->getIdMarca()->getProductos();

            switch ($nivel) {
                case 1:
                    $idCategoria = $this->idProducto->getIdCategoria();
                    break;
                case 2:
                    $idCategoria = $this->idProducto->getIdCategoria2();
                    break;
                case 3:
                    $idCategoria = $this->idProducto->getIdCategoria3();
                    break;
                case 4:
                    $idCategoria = $this->idProducto->getIdCategoria4();
                    break;
                case 5:
                    $idCategoria = $this->idProducto->getIdCategoria5();
                    break;
                case 6:
                    $idCategoria = $this->idProducto->getIdCategoria6();
                    break;
                case 7:
                    $idCategoria = $this->idProducto->getIdCategoria7();
                    break;
                case 8:
                    $idCategoria = $this->idProducto->getIdCategoria8();
                    break;
                case 9:
                    $idCategoria = $this->idProducto->getIdCategoria9();
                    break;
                case 10:
                    $idCategoria = $this->idProducto->getIdCategoria10();
                    break;
                case 11:
                    $idCategoria = $this->idProducto->getIdCategoria11();
                    break;
                default:
                    $idCategoria = $this->idProducto->getIdCategoria();
            }

            $idCategoria = $idCategoria->getIdCategoria();

            $arrayProductosPorMarcaYCategoria = $productos->filter(
                function (Producto $producto) use ($idCategoria) {
                    $idCategoria1  = !is_null($producto->getIdCategoria()) ? $producto->getIdCategoria()->getIdCategoria() : 0;
                    $idCategoria2  = !is_null($producto->getIdCategoria2()) ? $producto->getIdCategoria2()->getIdCategoria() : 0;
                    $idCategoria3  = !is_null($producto->getIdCategoria3()) ? $producto->getIdCategoria3()->getIdCategoria() : 0;
                    $idCategoria4  = !is_null($producto->getIdCategoria4()) ? $producto->getIdCategoria4()->getIdCategoria() : 0;
                    $idCategoria5  = !is_null($producto->getIdCategoria5()) ? $producto->getIdCategoria5()->getIdCategoria() : 0;
                    $idCategoria6  = !is_null($producto->getIdCategoria6()) ? $producto->getIdCategoria6()->getIdCategoria() : 0;
                    $idCategoria7  = !is_null($producto->getIdCategoria7()) ? $producto->getIdCategoria7()->getIdCategoria() : 0;
                    $idCategoria8  = !is_null($producto->getIdCategoria8()) ? $producto->getIdCategoria8()->getIdCategoria() : 0;
                    $idCategoria9  = !is_null($producto->getIdCategoria9()) ? $producto->getIdCategoria9()->getIdCategoria() : 0;
                    $idCategoria10 = !is_null($producto->getIdCategoria10()) ? $producto->getIdCategoria10()->getIdCategoria() : 0;
                    $idCategoria11 = !is_null($producto->getIdCategoria11()) ? $producto->getIdCategoria11()->getIdCategoria() : 0;

                    $categorias = [
                        $idCategoria1,
                        $idCategoria2,
                        $idCategoria3,
                        $idCategoria4,
                        $idCategoria5,
                        $idCategoria6,
                        $idCategoria7,
                        $idCategoria8,
                        $idCategoria9,
                        $idCategoria10,
                        $idCategoria11
                    ];


                    return (in_array($idCategoria, $categorias));
                }
            );

            return $arrayProductosPorMarcaYCategoria->toArray();
        }

        return [];
    }

    /**
     * Get nombreFiltro
     *
     * @return string
     */
    public function getNombreFiltro()
    {
        return $this->nombreFiltro;
    }

    /**
     * Set nombreFiltro
     *
     * @param string $nombreFiltro
     *
     * @return Promocion
     */
    public function setNombreFiltro($nombreFiltro)
    {
        $this->nombreFiltro = $nombreFiltro;

        return $this;
    }

    /**
     * Get creatividad
     *
     * @return Creatividad
     */
    public function getCreatividad()
    {
        return $this->creatividad;
    }

    /**
     * Set creatividad
     *
     * @param Creatividad $creatividad
     *
     * @return Promocion
     */
    public function setCreatividad(Creatividad $creatividad = null)
    {
        $this->creatividad = $creatividad;

        return $this;
    }
}