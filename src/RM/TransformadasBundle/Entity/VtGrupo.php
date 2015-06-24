<?php

namespace RM\TransformadasBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\TransformadasBundle\Form\VtIntervaloType;

/**
 * VtGrupo
 *
 * @ORM\Table(name="vt_grupo")
 * @ORM\Entity
 */
class VtGrupo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_grupo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGrupo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var \RM\TransformadasBundle\Entity\VtSegmento
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\VtSegmento", inversedBy="grupos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vt_segmento", referencedColumnName="id_vt_segmento")
     * })
     */
    private $idVtSegmento;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RM\TransformadasBundle\Entity\VtIntervalo", mappedBy="idGrupo", cascade={"persist",
     *                                                                          "remove"})
     */
    private $intervalos;


    /**
     * Set orden
     *
     * @param integer $orden
     *
     * @return VtGrupo
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }


    /**
     * Get idGrupo
     *
     * @return integer
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
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
     * Set idVtSegmento
     *
     * @param \RM\TransformadasBundle\Entity\VtSegmento $idVtSegmento
     *
     * @return VtGrupo
     */
    public function setIdVtSegmento(\RM\TransformadasBundle\Entity\VtSegmento $idVtSegmento = null)
    {
        $this->idVtSegmento = $idVtSegmento;

        return $this;
    }

    /**
     * Get idVtSegmento
     *
     * @return \RM\TransformadasBundle\Entity\VtSegmento
     */
    public function getIdVtSegmento()
    {
        return $this->idVtSegmento;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->idGrupo;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->intervalos = new ArrayCollection();
    }

    /**
     * Add intervalos
     *
     * @param \RM\TransformadasBundle\Entity\VtIntervalo $intervalos
     *
     * @return VtGrupo
     */
    public function addIntervalo(\RM\TransformadasBundle\Entity\VtIntervalo $intervalos)
    {
        $this->intervalos[] = $intervalos;

        return $this;
    }

    /**
     * Remove intervalos
     *
     * @param \RM\TransformadasBundle\Entity\VtIntervalo $intervalos
     */
    public function removeIntervalo(\RM\TransformadasBundle\Entity\VtIntervalo $intervalos)
    {
        $this->intervalos->removeElement($intervalos);
    }

    /**
     * Get intervalos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIntervalos()
    {
        return $this->intervalos->filter(function (VtIntervalo $intervalo) {
            return $intervalo->getEstado() > 1;
        });
    }
}