<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VidSegmentoGlobal
 *
 * @ORM\Table(name="vid_segmento_global")
 * @ORM\Entity
 */
class VidSegmentoGlobal
{
    /**
     * @var string
     * 
     * @ORM\Column(name="nombre", type="string", nullable=true)
     */
    private $nombre;

    /**
     * @var int
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
     * @var int
     * 
     * @ORM\Column(name="estado", type="smallint", nullable=true, options={"default" : 1})
     */
    private $estado;

    /**
     * @var integer
     * 
     * @ORM\Column(name="id_vid_segmento_global", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVidSegmentoGlobal;
    

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return VidSegmentoGlobal
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
     * Set condicion
     *
     * @param int $condicion
     * @return VidSegmentoGlobal
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;
    
        return $this;
    }

    /**
     * Get condicion
     *
     * @return int
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    /**
     * Set pivote
     *
     * @param float $pivote
     * @return VidSegmentoGlobal
     */
    public function setPivote($pivote)
    {
        $this->pivote = $pivote;
    
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
     * Set estado
     *
     * @param int $estado
     * @return VidSegmentoGlobal
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
     * Get idVidSegmentoGlobal
     *
     * @return integer 
     */
    public function getIdVidSegmento()
    {
        return $this->idVidSegmentoGlobal;
    }
}
