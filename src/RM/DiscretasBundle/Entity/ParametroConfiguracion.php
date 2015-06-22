<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParametroConfiguracion
 *
 * @ORM\Table(name="parametros_configuracion")
 * @ORM\Entity(repositoryClass="RM\DiscretasBundle\Entity\ParametroConfiguracionRepository")
 */
class ParametroConfiguracion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_param_configuracion", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string")
     *
     */
    private $codigo;

    /**
     * @var float
     *
     * @ORM\Column(name="maximo", type="float")
     */
    private $maximo;

    /**
     * @var float
     *
     * @ORM\Column(name="minimo", type="float")
     */
    private $minimo;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_medio", type="float")
     */
    private $valorMedio;

    /**
     * @var float
     *
     * @ORM\Column(name="c_variacion", type="float")
     */
    private $cVariacion;

    /**
     * @var float
     *
     * @ORM\Column(name="max_bajo", type="float")
     */
    private $maxBajo;

    /**
     * @var float
     *
     * @ORM\Column(name="max_medio", type="float")
     */
    private $maxMedio;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string")
     */
    private $descripcion;


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
     * Get maximo
     *
     * @return float
     */
    public function getMaximo()
    {
        return $this->maximo;
    }

    /**
     * Set maximo
     *
     * @param float $maximo
     *
     * @return ParametroConfiguracion
     */
    public function setMaximo($maximo)
    {
        $this->maximo = $maximo;

        return $this;
    }

    /**
     * Get minimo
     *
     * @return float
     */
    public function getMinimo()
    {
        return $this->minimo;
    }

    /**
     * Set minimo
     *
     * @param float $minimo
     *
     * @return ParametroConfiguracion
     */
    public function setMinimo($minimo)
    {
        $this->minimo = $minimo;

        return $this;
    }

    /**
     * Get valorMedio
     *
     * @return float
     */
    public function getValorMedio()
    {
        return $this->valorMedio;
    }

    /**
     * Set valorMedio
     *
     * @param float $valorMedio
     *
     * @return ParametroConfiguracion
     */
    public function setValorMedio($valorMedio)
    {
        $this->valorMedio = $valorMedio;

        return $this;
    }

    /**
     * Get cVariacion
     *
     * @return float
     */
    public function getCVariacion()
    {
        return $this->cVariacion;
    }

    /**
     * Set cVariacion
     *
     * @param float $cVariacion
     *
     * @return ParametroConfiguracion
     */
    public function setCVariacion($cVariacion)
    {
        $this->cVariacion = $cVariacion;

        return $this;
    }

    /**
     * Get maxBajo
     *
     * @return float
     */
    public function getMaxBajo()
    {
        return $this->maxBajo;
    }

    /**
     * Set maxBajo
     *
     * @param float $maxBajo
     *
     * @return ParametroConfiguracion
     */
    public function setMaxBajo($maxBajo)
    {
        $this->maxBajo = $maxBajo;

        return $this;
    }

    /**
     * Get maxMedio
     *
     * @return float
     */
    public function getMaxMedio()
    {
        return $this->maxMedio;
    }

    /**
     * Set maxMedio
     *
     * @param float $maxMedio
     *
     * @return ParametroConfiguracion
     */
    public function setMaxMedio($maxMedio)
    {
        $this->maxMedio = $maxMedio;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ParametroConfiguracion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

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
     * @return ParametroConfiguracion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return ParametroConfiguracion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}