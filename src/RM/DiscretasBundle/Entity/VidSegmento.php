<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VidSegmento
 *
 * @ORM\Table(name="vid_segmento")
 * @ORM\Entity
 */
class VidSegmento
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var smallint
     *
     * @ORM\Column(name="condicion", type="smallint", nullable=true)
     */
    private $condicion;

    /**
     * @var float
     *
     * @ORM\Column(name="pivote", type="float", nullable=true)
     */
    private $pivote;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;


    /**
     * @var integer
     *
     * @ORM\Column(name="id_vid_segmento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVidSegmento;

    /**
     * @var \RM\DiscretasBundle\Entity\VidGrupoSegmento
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\VidGrupoSegmento", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vid_grupo_segmento", referencedColumnName="id_vid_grupo_segmento")
     * })
     */
    private $idVidGrupoSegmento;

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
     * @return VidSegmento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return smallint
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get pivote
     *
     * @return float
     */
    public function getPivote()
    {
        return $this->pivote;
    }

    /**
     * Set pivote
     *
     * @param float $pivote
     *
     * @return VidSegmento
     */
    public function setPivote($pivote)
    {
        $this->pivote = $pivote;

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
     * @return VidSegmento
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get idVidSegmento
     *
     * @return integer
     */
    public function getIdVidSegmento()
    {
        return $this->idVidSegmento;
    }

    /**
     * Get idVidGrupoSegmento
     *
     * @return RM\DiscretasBundle\Entity\VidGrupoSegmento
     */
    public function getIdVidGrupoSegmento()
    {
        return $this->idVidGrupoSegmento;
    }

    /**
     * Set idVidGrupoSegmento
     *
     * @param RM\DiscretasBundle\Entity\VidGrupoSegmento $idVidGrupoSegmento
     *
     * @return VidSegmento
     */
    public function setIdVidGrupoSegmento(\RM\DiscretasBundle\Entity\VidGrupoSegmento $idVidGrupoSegmento = null)
    {
        $this->idVidGrupoSegmento = $idVidGrupoSegmento;

        return $this;
    }
}