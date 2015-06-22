<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Franja
 *
 * @ORM\Table(name="franja_horaria")
 * @ORM\Entity(repositoryClass="RM\DiscretasBundle\Entity\FranjaHorariaRepository")
 */
class FranjaHoraria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_franja", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id_franja;

    /**
     *
     * @var string @ORM\Column(name="franja", type="string", length=255, nullable=true)
     */
    private $franja;

    /**
     *
     * @var string @ORM\Column(name="valor_inicio", type="time", length=255, nullable=true)
     */
    private $valor_inicio;

    /**
     *
     * @var string @ORM\Column(name="valor_fin", type="time", length=255, nullable=true)
     */
    private $valor_fin;

    /**
     * Get franja
     *
     * @return string
     */
    public function getFranja()
    {
        return $this->franja;
    }

    /**
     * Set franja
     *
     * @param string $franja
     *
     * @return Franja
     */
    public function setFranja($franja)
    {
        $this->franja = $franja;

        return $this;
    }

    /**
     * Get valor_inicio
     *
     * @return string
     */
    public function getValorInicio()
    {
        return $this->valor_inicio;
    }

    /**
     * Set valor_inicio
     *
     * @param string $valor_inicio
     *
     * @return Franja
     */
    public function setValorInicio($valor_inicio)
    {
        $this->valor_inicio = $valor_inicio;

        return $this;
    }

    /**
     * Get valor_fin
     *
     * @return string
     */
    public function getValorFin()
    {
        return $this->valor_fin;
    }

    /**
     * Set valor_fin
     *
     * @param string $valor_fin
     *
     * @return Franja
     */
    public function setValorFin($valor_fin)
    {
        $this->valor_fin = $valor_fin;

        return $this;
    }
}