<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VidCriterioGlobal
 *
 * @ORM\Table(name="vid_criterio_global")
 * @ORM\Entity
 */
class VidCriterioGlobal implements VidCriterioInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="referencia_n", type="integer", nullable=true)
     */
    private $referenciaN;

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
     * @var integer
     *
     * @ORM\Column(name="id_vid_criterio_global", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idVidCriterioGlobal;

    /**
     * Set referenciaN
     *
     * @param integer $referenciaN
     *
     * @return VidCriterioGlobal
     */
    public function setReferenciaN($referenciaN)
    {
        $this->referenciaN = $referenciaN;

        return $this;
    }

    /**
     * Get referenciaN
     *
     * @return integer
     */
    public function getReferenciaN()
    {
        return $this->referenciaN;
    }

    /**
     * Set mesesN
     *
     * @param integer $mesesN
     *
     * @return VidCriterioGlobal
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
     *
     * @return VidCriterioGlobal
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
     * Get idVidCriterioGlobal
     *
     * @return integer
     */
    public function getIdVidCriterioGlobal()
    {
        return $this->idVidCriterioGlobal;
    }
}
