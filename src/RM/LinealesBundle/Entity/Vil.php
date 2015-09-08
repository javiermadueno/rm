<?php

namespace RM\LinealesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vil
 * 
 * @ORM\Table(name="vil")
 * @ORM\Entity(repositoryClass="RM\LinealesBundle\Entity\VilRepository")
 */
class Vil implements \JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var smallint
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Tipo")
     * @ORM\JoinColumn(name="id_tipo_variable", referencedColumnName="id_tipo_variable", nullable=false)
     */
    private $tipo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="solicita_tiempo", type="smallint", nullable=true)
     */
    private $solicitaTiempo;

    /**
     * @var integer
     *
     * @ORM\Column(name="meses_n", type="integer", nullable=true)
     */
    private $mesesN;

    /**
     * @var integer
     *
     * @ORM\Column(name="meses_m", type="integer", nullable=true)
     */
    private $mesesM;


    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_vil", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVil;



    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Vil
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
     * @return Vil
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
     * @return Vil
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
     * Set solicitaTiempo
     *
     * @param smallint $solicitaTiempo
     * @return Vil
     */
    public function setSolicitaTiempo($solicitaTiempo)
    {
        $this->solicitaTiempo = $solicitaTiempo;
    
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
     * Set mesesN
     *
     * @param integer $mesesN
     * @return Vil
     */
    public function setMesesN($mesesN)
    {
        $this->mesesN = $mesesN;
    
        return $this;
    }

    /**
     * Get mesesN
     *
     * @return integer 
     */
    public function getMesesN()
    {
        return $this->mesesN;
    }

    /**
     * Set mesesM
     *
     * @param integer $mesesM
     * @return Vil
     */
    public function setMesesM($mesesM)
    {
        $this->mesesM = $mesesM;
    
        return $this;
    }

    /**
     * Get mesesM
     *
     * @return integer 
     */
    public function getMesesM()
    {
        return $this->mesesM;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
     * @return Vil
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
     * Get idVil
     *
     * @return integer 
     */
    public function getIdVil()
    {
        return $this->idVil;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->idVil,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'mesesM' => $this->mesesM,
            'mesesN' => $this->mesesN,
            'estado' => $this->estado,
            'solicitaTiempo' => $this->solicitaTiempo,
        ];
    }
}