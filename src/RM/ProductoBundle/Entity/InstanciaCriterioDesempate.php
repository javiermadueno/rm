<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstanciaCriterioDesempate
 *
 * @ORM\Table(name="instancia_criterios_desempate")
 * @ORM\Entity
 */
class InstanciaCriterioDesempate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_instanc_crit", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\CriterioDesempate")
     * @ORM\JoinColumn(name="id_crit_desempate", referencedColumnName="id_crit_desempate", nullable=false)
     */
    private $criterioDesempate;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\GrupoSlots")
     * @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo", nullable=false)
     */
    private $grupo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\InstanciaComunicacion")
     * @ORM\JoinColumn(name="id_instancia", referencedColumnName="id_instancia", nullable=false)
     */
    private $idInstancia;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_slot", type="integer")
     */
    private $numSlot;

    /**
     * @var integer
     *
     * @ORM\Column(name="peso", type="integer")
     */
    private $peso;


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
     * Set numSlot
     *
     * @param integer $numSlot
     * @return InstanciaCriterioDesempate
     */
    public function setNumSlot($numSlot)
    {
        $this->numSlot = $numSlot;
    
        return $this;
    }

    /**
     * Get numSlot
     *
     * @return integer 
     */
    public function getNumSlot()
    {
        return $this->numSlot;
    }

    /**
     * Set peso
     *
     * @param integer $peso
     * @return InstanciaCriterioDesempate
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    
        return $this;
    }

    /**
     * Get peso
     *
     * @return integer 
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set grupo
     *
     * @param \RM\PlantillaBundle\Entity\GrupoSlots $grupo
     * @return InstanciaCriterioDesempate
     */
    public function setGrupo(\RM\PlantillaBundle\Entity\GrupoSlots $grupo = null)
    {
        $this->grupo = $grupo;
    
        return $this;
    }

    /**
     * Get grupo
     *
     * @return \RM\PlantillaBundle\Entity\GrupoSlots 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set idInstancia
     *
     * @param \RM\ComunicacionBundle\Entity\InstanciaComunicacion $idInstancia
     * @return InstanciaCriterioDesempate
     */
    public function setIdInstancia(\RM\ComunicacionBundle\Entity\InstanciaComunicacion $idInstancia = null)
    {
        $this->idInstancia = $idInstancia;
    
        return $this;
    }

    /**
     * Get idInstancia
     *
     * @return \RM\ComunicacionBundle\Entity\InstanciaComunicacion 
     */
    public function getIdInstancia()
    {
        return $this->idInstancia;
    }

    /**
     * Set criterioDesempate
     *
     * @param \RM\ProductoBundle\Entity\CriterioDesempate $criterioDesempate
     * @return InstanciaCriterioDesempate
     */
    public function setCriterioDesempate(\RM\ProductoBundle\Entity\CriterioDesempate $criterioDesempate = null)
    {
        $this->criterioDesempate = $criterioDesempate;
    
        return $this;
    }

    /**
     * Get criterioDesempate
     *
     * @return \RM\ProductoBundle\Entity\CriterioDesempate 
     */
    public function getCriterioDesempate()
    {
        return $this->criterioDesempate;
    }
}