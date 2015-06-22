<?php

namespace RM\LinealesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VilSegmento
 *
 * @ORM\Table(name="vil_segmento")
 * @ORM\Entity
 */
class VilSegmento
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_vil_segmento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vil", type="integer")
     * @ORM\ManyToOne(targetEntity="RM\LinealesBundle\Entity\Vil")
     */
    private $idVil;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor", type="integer")
     */
    private $valor;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;


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
     * Get idVil
     *
     * @return integer
     */
    public function getIdVil()
    {
        return $this->idVil;
    }

    /**
     * Set idVil
     *
     * @param integer $idVil
     *
     * @return VilSegmento
     */
    public function setIdVil($idVil)
    {
        $this->idVil = $idVil;

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
     * @return VilSegmento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get valor
     *
     * @return integer
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     *
     * @return VilSegmento
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     *
     * @return VilSegmento
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }
}