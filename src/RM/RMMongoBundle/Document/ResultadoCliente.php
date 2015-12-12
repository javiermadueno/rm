<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/05/2015
 * Time: 17:24
 */

namespace RM\RMMongoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/**
 * Class ResultadoCliente
 * @package RM\RMMongoBundle\Document
 *
 * @Mongo\Document(collection="res_cliente", repositoryClass="RM\RMMongoBundle\Document\ResultadoClienteRepository")
 */
class ResultadoCliente
{
    /**
     * @Mongo\Id()
     */
    private $id;

    /**
     * @Mongo\Field(type="string", name="fecha")
     */
    private $fecha;

    /**
     * @Mongo\Field(type="int", name="cli")
     */
    private $cliente;

    /**
     * @Mongo\Field(type="float", name="c")
     */
    private $contribucion;

    /**
     * @Mongo\Field(type="float", name="f")
     */
    private $frecuencia;

    /**
     * @Mongo\Field(type="float", name="tm")
     */
    private $ticketMedio;

    /**
     * @Mongo\Field(type="float", name="g")
     */
    private $gama;

    /**
     * @Mongo\Field(type="float", name="a1")
     */
    private $amplitu1;

    /**
     * @Mongo\Field(type="float", name="a2")
     */
    private $amplitud2;

    /**
     * @Mongo\Field(type="float", name="a3")
     */
    private $amplitud3;

    /**
     * @Mongo\Field(type="float", name="rec")
     */
    private $recencia;

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
     * Set fecha
     *
     * @param string $fecha
     * @return self
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
        return $this;
    }

    /**
     * Get fecha
     *
     * @return string $fecha
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set cliente
     *
     * @param int $cliente
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
     * @return int $cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set contribucion
     *
     * @param float $contribucion
     * @return self
     */
    public function setContribucion($contribucion)
    {
        $this->contribucion = $contribucion;
        return $this;
    }

    /**
     * Get contribucion
     *
     * @return float $contribucion
     */
    public function getContribucion()
    {
        return $this->contribucion;
    }

    /**
     * Set frecuencia
     *
     * @param float $frecuencia
     * @return self
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;
        return $this;
    }

    /**
     * Get frecuencia
     *
     * @return float $frecuencia
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * Set ticketMedio
     *
     * @param float $ticketMedio
     * @return self
     */
    public function setTicketMedio($ticketMedio)
    {
        $this->ticketMedio = $ticketMedio;
        return $this;
    }

    /**
     * Get ticketMedio
     *
     * @return float $ticketMedio
     */
    public function getTicketMedio()
    {
        return $this->ticketMedio;
    }

    /**
     * Set gama
     *
     * @param float $gama
     * @return self
     */
    public function setGama($gama)
    {
        $this->gama = $gama;
        return $this;
    }

    /**
     * Get gama
     *
     * @return float $gama
     */
    public function getGama()
    {
        return $this->gama;
    }

    /**
     * Set amplitu1
     *
     * @param float $amplitu1
     * @return self
     */
    public function setAmplitu1($amplitu1)
    {
        $this->amplitu1 = $amplitu1;
        return $this;
    }

    /**
     * Get amplitu1
     *
     * @return float $amplitu1
     */
    public function getAmplitu1()
    {
        return $this->amplitu1;
    }

    /**
     * Set amplitud2
     *
     * @param float $amplitud2
     * @return self
     */
    public function setAmplitud2($amplitud2)
    {
        $this->amplitud2 = $amplitud2;
        return $this;
    }

    /**
     * Get amplitud2
     *
     * @return float $amplitud2
     */
    public function getAmplitud2()
    {
        return $this->amplitud2;
    }

    /**
     * Set amplitud3
     *
     * @param float $amplitud3
     * @return self
     */
    public function setAmplitud3($amplitud3)
    {
        $this->amplitud3 = $amplitud3;
        return $this;
    }

    /**
     * Get amplitud3
     *
     * @return float $amplitud3
     */
    public function getAmplitud3()
    {
        return $this->amplitud3;
    }

    /**
     * Set recencia
     *
     * @param float $recencia
     * @return self
     */
    public function setRecencia($recencia)
    {
        $this->recencia = $recencia;
        return $this;
    }

    /**
     * Get recencia
     *
     * @return float $recencia
     */
    public function getRecencia()
    {
        return $this->recencia;
    }
}
