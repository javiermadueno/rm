<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slot
 *
 * @ORM\Table(name="slot")
 * @ORM\Entity
 */
class Slot
{
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_slot", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSlot;

    /**
     * @var \RM\PlantillaBundle\Entity\GrupoSlots
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\GrupoSlots", inversedBy="slots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo")
     * })
     */
    private $idGrupo;


    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Slot
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
     * Set estado
     *
     * @param smallint $estado
     *
     * @return Slot
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
     * Get idSlot
     *
     * @return integer
     */
    public function getIdSlot()
    {
        return $this->idSlot;
    }

    /**
     * Set idGrupo
     *
     * @param \RM\PlantillaBundle\Entity\GrupoSlots $idGrupo
     *
     * @return Slot
     */
    public function setIdGrupo(\RM\PlantillaBundle\Entity\GrupoSlots $idGrupo = null)
    {
        $this->idGrupo = $idGrupo;

        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return \RM\PlantillaBundle\Entity\GrupoSlots
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }
}