<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vid
 *
 * @ORM\Table(name="vid")
 * @ORM\Entity(repositoryClass="RM\DiscretasBundle\Entity\VidRepository")
 */
class Vid implements \JsonSerializable
{
    const NO_SOLICITA_TIEMPO = 0;
    const SOLICITA_N = 1;
    const SOLICITA_N_M = 2;

    const CLASIFICACION_CATEGORIA = 1;
    const CLASIFICACION_PROVEEDOR = 2;
    const CLASIFICACION_MARCA = 3;
    const CLASFICACION_SI_NO = 4;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var Tipo
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Tipo")
     * @ORM\JoinColumn(name="id_tipo_variable", referencedColumnName="id_tipo_variable", nullable=false)
     */
    private $tipo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="clasificacion", type="smallint", nullable=true)
     */
    private $clasificacion;

    /**
     * @var smallint
     *
     * @ORM\Column(name="solicita_tiempo", type="smallint", nullable=true)
     */
    private $solicitaTiempo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVid;

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
     * @return Vid
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

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
     * @return Vid
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return Tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipo
     *
     * @param Tipo $tipo
     *
     * @return Vid
     */
    public function setTipo(Tipo $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return smallint
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }

    /**
     * Set clasificacion
     *
     * @param smallint $clasificacion
     *
     * @return Vid
     */
    public function setClasificacion($clasificacion)
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    /**
     * Get solicitaTiempo
     *
     * @return smallint
     */
    public function getSolicitaTiempo()
    {
        return $this->solicitaTiempo;
    }

    /**
     * Set solicitaTiempo
     *
     * @param smallint $solicitaTiempo
     *
     * @return Vid
     */
    public function setSolicitaTiempo($solicitaTiempo)
    {
        $this->solicitaTiempo = $solicitaTiempo;

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
     * Set estado
     *
     * @param smallint $estado
     *
     * @return Vid
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get idVid
     *
     * @return integer
     */
    public function getIdVid()
    {
        return $this->idVid;
    }

    public function jsonSerialize()
    {
        return [
            'id'             => $this->idVid,
            'nombre'         => $this->nombre,
            'descripcion'    => $this->descripcion,
            'clasificacion'  => $this->clasificacion,
            'estado'         => $this->estado,
            'solicitaTiempo' => $this->solicitaTiempo,
        ];
    }

}