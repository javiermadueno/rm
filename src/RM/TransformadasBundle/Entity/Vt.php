<?php

namespace RM\TransformadasBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vt
 *
 * @ORM\Table(name="vt")
 * @ORM\Entity(repositoryClass="RM\TransformadasBundle\Entity\VtRepository")
 */
class Vt implements \JsonSerializable
{
    const TIPO_CICLO_VIDA = 5;
    const TIPO_OTRAS_TRANSFORMADAS = 6;

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
     * @var \RM\DiscretasBundle\Entity\Tipo
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Tipo")
     * @ORM\JoinColumn(name="id_tipo_variable", referencedColumnName="id_tipo_variable", nullable=false)
     */
    private $tipo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vt", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RM\TransformadasBundle\Entity\VtSegmento", mappedBy="idVt", cascade={"persist",
     *                                                                         "remove"})
     */
    private $segmentos;


    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Vt
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Vt
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

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
     * Set tipo
     *
     * @param smallint $tipo
     *
     * @return Vt
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
     * Set estado
     *
     * @return Vt
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
     * Get idVt
     *
     * @return integer
     */
    public function getIdVt()
    {
        return $this->idVt;
    }

    public function __toString()
    {
        return (string)$this->idVt;
    }

    public function jsonSerialize()
    {
        return [
            'id'          => $this->idVt,
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado'      => $this->estado,
        ];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->segmentos = new ArrayCollection();
    }

    /**
     * Add segmentos
     *
     * @param \RM\TransformadasBundle\Entity\VtSegmento $segmentos
     *
     * @return Vt
     */
    public function addSegmento(\RM\TransformadasBundle\Entity\VtSegmento $segmentos)
    {
        $this->segmentos[] = $segmentos;

        return $this;
    }

    /**
     * Remove segmentos
     *
     * @param \RM\TransformadasBundle\Entity\VtSegmento $segmentos
     */
    public function removeSegmento(\RM\TransformadasBundle\Entity\VtSegmento $segmentos)
    {
        $this->segmentos->removeElement($segmentos);
    }

    /**
     * Get segmentos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSegmentos()
    {
        return $this->segmentos->filter(function (VtSegmento $segmento) {
            return $segmento->getEstado() > -1;
        });
    }
}