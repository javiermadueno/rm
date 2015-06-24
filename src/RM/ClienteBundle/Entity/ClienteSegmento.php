<?php

namespace RM\ClienteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClienteSegmento
 *
 * @ORM\Table(name="cliente_segmento")
 * @ORM\Entity
 */
class ClienteSegmento
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cliente_segmento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idClienteSegmento;

    /**
     * @var \RM\SegmentoBundle\Entity\Segmento
     *
     * @ORM\ManyToOne(targetEntity="RM\SegmentoBundle\Entity\Segmento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_segmento", referencedColumnName="id_segmento")
     * })
     */
    private $idSegmento;

    /**
     * @var \RM\ClienteBundle\Entity\Cliente
     *
     * @ORM\ManyToOne(targetEntity="RM\ClienteBundle\Entity\Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cliente", referencedColumnName="id_cliente")
     * })
     */
    private $idCliente;


    /**
     * Get idClienteSegmento
     *
     * @return integer
     */
    public function getIdClienteSegmento()
    {
        return $this->idClienteSegmento;
    }

    /**
     * Set idSegmento
     *
     * @param \RM\SegmentoBundle\Entity\Segmento $idSegmento
     *
     * @return ClienteSegmento
     */
    public function setIdSegmento(\RM\SegmentoBundle\Entity\Segmento $idSegmento = null)
    {
        $this->idSegmento = $idSegmento;

        return $this;
    }

    /**
     * Get idSegmento
     *
     * @return \RM\SegmentoBundle\Entity\Segmento
     */
    public function getIdSegmento()
    {
        return $this->idSegmento;
    }

    /**
     * Set idCliente
     *
     * @param \RM\ClienteBundle\Entity\Cliente $idCliente
     *
     * @return ClienteSegmento
     */
    public function setIdCliente(\RM\ClienteBundle\Entity\Cliente $idCliente = null)
    {
        $this->idCliente = $idCliente;

        return $this;
    }

    /**
     * Get idCliente
     *
     * @return \RM\ClienteBundle\Entity\Cliente
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }
}