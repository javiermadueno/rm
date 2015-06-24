<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPromocion
 *
 * @ORM\Table(name="tipo_promocion")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\TipoPromocionRepository")
 */
class TipoPromocion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_promocion", type="smallint", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTipoPromocion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=6, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer",length=11, nullable=true)
     */
    private $orden;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", length=6, nullable=true)
     */
    private $estado;

    /*GETTERS Y SETTERS*/

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return string
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
     * Set orden
     *
     * @param int $orden
     *
     * @return TipoPromocion
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return TipoPromocion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

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
     * Set estado
     *
     * @param smallint $estado
     *
     * @return Promocion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return smallint
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Get idTipoPromocion
     *
     * @return integer
     */
    public function getIdTipoPromocion()
    {
        return $this->idTipoPromocion;
    }
}