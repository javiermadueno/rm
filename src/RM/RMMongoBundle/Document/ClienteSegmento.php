<?php

namespace RM\RMMongoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Doctrine\Common\Collections\Collection;

/**
 * ClienteSegmento
 *
 * @Mongo\Document(collection="cliente_segmento", repositoryClass="RM\RMMongoBundle\Document\ClienteSegmentoRepository")
 */
class ClienteSegmento
{
    /**
     * @var string
     *
     * @Mongo\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Mongo\Field(name="cli", type="int")
     */
    private $cliente;

    /**
     * @var integer
     *
     * @Mongo\Collection(name="ls")
     */
    private $segmento;


    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cliente
     *
     * @param string $cliente
     * @return self
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
        return $this;
    }

    /**
     * Get cliente
     *
     * @return string $cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set segmento
     *
     * @param collection $segmento
     * @return self
     */
    public function setSegmento($segmento)
    {
        $this->segmento = $segmento;
        return $this;
    }

    /**
     * Get segmento
     *
     * @return collection $segmento
     */
    public function getSegmento()
    {
        return $this->segmento;
    }
}
