<?php

namespace RM\DiscretasBundle\Entity;

interface VidCriterioInterface
{

    /**
     * Set mesesN
     *
     * @param integer $mesesN
     *
     * @return VidCriterioInterface
     */
    public function setMesesN($mesesN);

    /**
     * Get mesesN
     *
     * @return integer
     */
    public function getMesesN();

    /**
     * Set mesesM
     *
     * @param integer $mesesM
     *
     * @return VidCriterioInterface
     */
    public function setMesesM($mesesM);

    /**
     * Get mesesM
     *
     * @return integer
     */
    public function getMesesM();
}

?>