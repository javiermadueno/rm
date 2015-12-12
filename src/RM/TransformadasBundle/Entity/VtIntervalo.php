<?php

namespace RM\TransformadasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VtIntervalo
 *
 * @ORM\Table(name="vt_intervalo")
 * @ORM\Entity
 */
class VtIntervalo
{
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
     * @var float
     *
     * @ORM\Column(name="factor", type="float", nullable=true)
     */
    private $factor;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_intervalo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idIntervalo;
    
    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var \RM\LinealesBundle\Entity\Vil
     *
     * @ORM\ManyToOne(targetEntity="RM\LinealesBundle\Entity\Vil", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vil", referencedColumnName="id_vil")
     * })
     */
    private $idVil;

    /**
     * @var \RM\TransformadasBundle\Entity\VtGrupo
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\VtGrupo", inversedBy="intervalos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo")
     * })
     */
    private $idGrupo;



    /**
     * Set condicion
     *
     * @param smallint $condicion
     * @return VtIntervalo
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;
    
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

    /**
     * Set pivote
     *
     * @param float $pivote
     * @return VtIntervalo
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
     * Set factor
     *
     * @param float $factor
     * @return VtIntervalo
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;
    
        return $this;
    }

    /**
     * Get factor
     *
     * @return float 
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Get idIntervalo
     *
     * @return integer 
     */
    public function getIdIntervalo()
    {
        return $this->idIntervalo;
    }
    
    /**
     * Set estado
     *
     * @param smallint $estado
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
     * Set idVil
     *
     * @param \RM\LinealesBundle\Entity\Vil $idVil
     * @return VtIntervalo
     */
    public function setIdVil(\RM\LinealesBundle\Entity\Vil $idVil = null)
    {
        $this->idVil = $idVil;
    
        return $this;
    }

    /**
     * Get idVil
     *
     * @return \RM\LinealesBundle\Entity\Vil 
     */
    public function getIdVil()
    {
        return $this->idVil;
    }

    /**
     * Set idGrupo
     *
     * @param \RM\TransformadasBundle\Entity\VtGrupo $idGrupo
     * @return VtIntervalo
     */
    public function setIdGrupo(\RM\TransformadasBundle\Entity\VtGrupo $idGrupo = null)
    {
        $this->idGrupo = $idGrupo;
    
        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return \RM\TransformadasBundle\Entity\VtGrupo 
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }
    
    public function __toString()
    {
    	return (string)$this->idIntervalo;
    }
}