<?php

namespace RM\TransformadasBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * VtSegmento
 *
 * @ORM\Table(name="vt_segmento")
 * @ORM\Entity
 */
class VtSegmento
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
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vt_segmento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVtSegmento;

    /**
     * @var \RM\TransformadasBundle\Entity\Vt
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\Vt", inversedBy="segmentos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vt", referencedColumnName="id_vt")
     * })
     */
    private $idVt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RM\TransformadasBundle\Entity\VtGrupo", mappedBy="idVtSegmento",
     *                                                                      cascade={"persist", "remove"})
     */
    private $grupos;


    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return VtSegmento
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
     * Set estado
     *
     * @param smallint $estado
     *
     * @return VtSegmento
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
     * Get idVtSegmento
     *
     * @return integer
     */
    public function getIdVtSegmento()
    {
        return $this->idVtSegmento;
    }

    /**
     * Set idVt
     *
     * @param \RM\TransformadasBundle\Entity\Vt $idVt
     *
     * @return VtSegmento
     */
    public function setIdVt(\RM\TransformadasBundle\Entity\Vt $idVt = null)
    {
        $this->idVt = $idVt;

        return $this;
    }

    /**
     * Get idVt
     *
     * @return \RM\TransformadasBundle\Entity\Vt
     */
    public function getIdVt()
    {
        return $this->idVt;
    }

    public function __toString()
    {
        return (string)$this->idVtSegmento;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->grupos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add grupos
     *
     * @param \RM\TransformadasBundle\Entity\VtGrupo $grupos
     *
     * @return VtSegmento
     */
    public function addGrupo(\RM\TransformadasBundle\Entity\VtGrupo $grupos)
    {
        $this->grupos[] = $grupos;

        return $this;
    }

    /**
     * Remove grupos
     *
     * @param \RM\TransformadasBundle\Entity\VtGrupo $grupos
     */
    public function removeGrupo(\RM\TransformadasBundle\Entity\VtGrupo $grupos)
    {
        $this->grupos->removeElement($grupos);
    }

    /**
     * Get grupos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos->filter(function (VtGrupo $grupo) {
            return $grupo->getEstado() > -1;
        });
    }
}