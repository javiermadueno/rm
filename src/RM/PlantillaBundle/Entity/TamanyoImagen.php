<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TamanyoImagen
 *
 * @ORM\Table(name="tamanyo_imagen")
 * @ORM\Entity(repositoryClass="RM\PlantillaBundle\Entity\TamanyoImagenRepository")
 */
class TamanyoImagen
{
    const MARCA = 1;
    const PRODUCTO = 0;
    const CREATIVIDAD = 3;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=80, nullable=true)
     */
    private $codigo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="tipo", type="smallint", nullable=true)
     */
    private $tipo;

    /**
     * @var float
     *
     * @ORM\Column(name="ancho", type="float", nullable=true)
     */
    private $ancho;

    /**
     * @var float
     *
     * @ORM\Column(name="alto", type="float", nullable=true)
     */
    private $alto;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tamanyo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTamanyo;


    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return TamanyoImagen
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
     * Set tipo
     *
     * @param smallint $tipo
     *
     * @return TamanyoImagen
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return smallint
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set ancho
     *
     * @param float $ancho
     *
     * @return TamanyoImagen
     */
    public function setAncho($ancho)
    {
        $this->ancho = $ancho;

        return $this;
    }

    /**
     * Get ancho
     *
     * @return float
     */
    public function getAncho()
    {
        return $this->ancho;
    }

    /**
     * Set alto
     *
     * @param float $alto
     *
     * @return TamanyoImagen
     */
    public function setAlto($alto)
    {
        $this->alto = $alto;

        return $this;
    }

    /**
     * Get alto
     *
     * @return float
     */
    public function getAlto()
    {
        return $this->alto;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
     *
     * @return TamanyoImagen
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
     * Get idTamanyo
     *
     * @return integer
     */
    public function getIdTamanyo()
    {
        return $this->idTamanyo;
    }
}