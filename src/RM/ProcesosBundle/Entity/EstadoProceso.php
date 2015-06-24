<?php

namespace RM\ProcesosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoProceso
 *
 * @ORM\Table(name="estado_proceso")
 * @ORM\Entity(repositoryClass="RM\ProcesosBundle\Entity\EstadoProcesoRepository")
 */
class EstadoProceso
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_estado_proceso", type="integer")
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
     * @return EstadoProceso
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
     * @return EstadoProceso
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
