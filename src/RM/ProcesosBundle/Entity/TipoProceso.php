<?php

namespace RM\ProcesosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoProceso
 *
 * @ORM\Table(name="tipo_proceso")
 * @ORM\Entity(repositoryClass="RM\ProcesosBundle\Entity\TipoProcesoRepository")
 */
class TipoProceso
{

    const P00   = 'P00';
    const P01   = 'P01';
    const P02   = 'P02';
    const P03   = 'P03';
    const P04   = 'P04';
    const P05   = 'P05';
    const P01_2 = 'P01.2';
    const P07   = 'P07';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_proceso", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;


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
     * Set codigo
     *
     * @param string $codigo
     * @return TipoProceso
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
     * Set nombre
     *
     * @param string $nombre
     * @return TipoProceso
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

    public function __toString()
    {
        return $this->id.'';
    }
}
