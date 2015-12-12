<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 16/07/2015
 * Time: 10:01
 */

namespace RM\ComunicacionBundle\Entity;



use RM\ComunicacionBundle\Model\Abstracts\InstanciaDecoratorAbstract;
use RM\PlantillaBundle\Entity\GrupoSlots;


class Campaign extends InstanciaDecoratorAbstract
{
    /**
     * @return int
     */
    protected function getTipoNumPromocion()
    {
        return GrupoSlots::PROMOCION;
    }

    public function getPromocionesSegmentadas()
    {
        if (!$this->segmentadas->isEmpty()) {
            $segmentadas = [];
            foreach ($this->getNumPromociones() as $numPromocion) {
                $segmentadas = array_merge(
                    $segmentadas,
                    $numPromocion
                        ->getSegmentadas()
                        ->toArray()
                );
            }

            $this->segmentadas = new ArrayCollection($segmentadas);
        }

        return $this->segmentadas;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenericas()
    {
        if (!$this->genericas->isEmpty()) {
            $genericas = [];
            foreach ($this->getNumPromociones() as $numPromocion) {
                $genericas = array_merge(
                    $genericas,
                    $numPromocion
                        ->getGenericas()
                        ->toArray()
                );
            }

            $this->genericas = new ArrayCollection($genericas);
        }

        return $this->genericas;

    }

} 