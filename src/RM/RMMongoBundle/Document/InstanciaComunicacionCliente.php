<?php

namespace RM\RMMongoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/**
 * ClienteSegmento
 *
 * @Mongo\Document(collection="instancia_comunicacion_cliente", repositoryClass="InstanciaComunicacionClienteRepository")
 * @Mongo\Index(keys={"_id.id_instancia", "_id.id_cliente", "_id.id_slot"})
 */
class InstanciaComunicacionCliente implements \JsonSerializable
{
    /**
     * @var string
     *
     * @Mongo\Id()
     */
    private $id;

    /**
     * @var integer
     *
     * @Mongo\Field(name="cli", type="int")
     */
    private $id_cliente;

    /**
     * @var integer
     *
     * @Mongo\Field(name="i", type="int")
     */
    private $id_instancia;

    /**
     * @var array
     *
     * @Mongo\Field(name="promo", type="hash")
     */
    private $promocion;

    /**
     * @var integer
     *
     * @Mongo\Field(name="s", type="int")
     */
    private $id_slot;

    /**
     * @var array
     *
     * @Mongo\Field(name="creatividad", type="hash")
     */
    private $creatividad;


    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cliente
     *
     * @param int $cliente
     *
     * @return self
     */
    public function setCliente($cliente)
    {
        $this->id_cliente = $cliente;
        return $this;
    }

    /**
     * Get cliente
     *
     * @return int $cliente
     */
    public function getCliente()
    {
        return $this->id_cliente;
    }

    /**
     * Set instancia
     *
     * @param int $instancia
     *
     * @return self
     */
    public function setInstancia($instancia)
    {
        $this->id_instancia = $instancia;
        return $this;
    }

    /**
     * Get instancia
     *
     * @return int $instancia
     */
    public function getInstancia()
    {
        return $this->id_instancia;
    }

    /**
     * Get promocion
     *
     * @return int $promocion
     */
    public function getPromocion()
    {
        return $this->promocion;
    }

    /**
     * Set promocion
     *
     * @param int $promocion
     *
     * @return self
     */
    public function setPromocion($promocion)
    {
        $this->promocion = $promocion;
        return $this;
    }

    /**
     * Set slot
     *
     * @param int $slot
     *
     * @return self
     */
    public function setSlot($slot)
    {
        $this->id_slot = $slot;
        return $this;
    }

    /**
     * Get slot
     *
     * @return int $slot
     */
    public function getSlot()
    {
        return $this->id_slot;
    }

    public function jsonSerialize()
    {
        return [
            'id'        => $this->id,
            'instancia' => $this->id_instancia,
            'cliente'   => $this->id_cliente,
            'slot'      => $this->id_slot,
        ];
    }

    /**
     * Get idCliente
     *
     * @return int $idCliente
     */
    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    /**
     * Set idCliente
     *
     * @param int $idCliente
     *
     * @return self
     */
    public function setIdCliente($idCliente)
    {
        $this->id_cliente = $idCliente;
        return $this;
    }

    /**
     * Get idInstancia
     *
     * @return int $idInstancia
     */
    public function getIdInstancia()
    {
        return $this->id_instancia;
    }

    /**
     * Set idInstancia
     *
     * @param int $idInstancia
     *
     * @return self
     */
    public function setIdInstancia($idInstancia)
    {
        $this->id_instancia = $idInstancia;
        return $this;
    }

    /**
     * Get idSlot
     *
     * @return int $idSlot
     */
    public function getIdSlot()
    {
        return $this->id_slot;
    }

    /**
     * Set idSlot
     *
     * @param int $idSlot
     *
     * @return self
     */
    public function setIdSlot($idSlot)
    {
        $this->id_slot = $idSlot;
        return $this;
    }

    /**
     * Get creatividad
     *
     * @return hash $creatividad
     */
    public function getCreatividad()
    {
        return $this->creatividad;
    }

    /**
     * Set creatividad
     *
     * @param hash $creatividad
     *
     * @return self
     */
    public function setCreatividad($creatividad)
    {
        $this->creatividad = $creatividad;
        return $this;
    }
}