<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 22/04/2015
 * Time: 9:25
 */

namespace RM\RMMongoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/**
 * Class ResultadoMensual
 * @package RM\RMMongoBundle\Document
 * @Mongo\Document(collection="res_mensual", repositoryClass="RM\RMMongoBundle\Document\ResultadoMensualRepository")
 */
class ResultadoMensual
{

    public function __construct()
    {
        $this->getFecha();
    }

    private $fecha;

    /**
     * El id es la fecha con el formato 'YYYY-mm'
     *
     * @Mongo\Id(strategy="NONE")
     */
    private $id;

    /**
     * Ventas totales en euros
     *
     * @Mongo\Field(name="ventasTot", type="float")
     */
    private $ventasTotales;

    /**
     * Ventas de miembros en euros
     *
     * @Mongo\Field(name="ventasCli", type="float")
     */
    private $ventasCliente;

    /**
     * Ventas de no miembros en euros
     *
     * @Mongo\Field(name="ventasNoCli", type="float")
     */
    private $ventasNoCliente;

    /**
     * Porcentaje de contribucion de los miembros
     *
     * @Mongo\Field(name="contribCli", type="float")
     */
    private $contribucionClientes;

    /**
     * Numero de compras de miembros
     *
     * @Mongo\Field(name="numComprasCli", type="int")
     */
    private $numeroComprasCliente;

    /**
     * Numero de compras de no miembros
     *
     * @Mongo\Field(name="numComprasNoCli", type="int")
     */
    private $numeroComprasNoCliente;

    /**
     * @Mongo\Field(name="numCli", type="int")
     */
    private $numeroClientes;

    /**
     * @Mongo\Field(name="numCliCompran", type="int")
     */
    private $numeroClientesQueCompran;

    /**
     * Media de dias sin comprar de miemrbos
     *
     * @Mongo\Field(name="recenciaCli", type="float")
     */
    private $recenciaClientes;

    /**
     * Media de frecuencia de miembros
     *
     * @Mongo\Field(name="frecCli", type="float")
     */
    private $frecuenciaClientes;

    /**
     * Media de euros por ticket de miembros
     *
     * @Mongo\Field(name="ticketCli", type="float")
     */
    private $ticketClientes;

    /**
     * Media de amplitud de medios
     *
     * @Mongo\Field(name="ampliCli1", type="float")
     */
    private $amplitudClientes1;

    /**
     * Media de amplitud de medios
     *
     * @Mongo\Field(name="ampliCli2", type="float")
     */
    private $amplitudClientes2;

    /**
     * Media de amplitud de medios
     *
     * @Mongo\Field(name="ampliCli3", type="float")
     */
    private $amplitudClientes3;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFecha()
    {
        if (!$this->fecha) {
            if (isset($this->id)) {
                list($year, $month) = explode('-', $this->id);
                $this->fecha = new \DateTime(sprintf('01-%s-%s', $month, $year));
            }
        }

        return $this->fecha;
    }

    /**
     * Set ventasTotales
     *
     * @param float $ventasTotales
     *
     * @return self
     */
    public function setVentasTotales($ventasTotales)
    {
        $this->ventasTotales = $ventasTotales;
        return $this;
    }

    /**
     * Get ventasTotales
     *
     * @return float $ventasTotales
     */
    public function getVentasTotales()
    {
        return $this->ventasTotales;
    }

    /**
     * Set ventasCliente
     *
     * @param float $ventasCliente
     *
     * @return self
     */
    public function setVentasCliente($ventasCliente)
    {
        $this->ventasCliente = $ventasCliente;
        return $this;
    }

    /**
     * Get ventasCliente
     *
     * @return float $ventasCliente
     */
    public function getVentasCliente()
    {
        return round($this->ventasCliente, 2);
    }

    /**
     * Set ventasNoCliente
     *
     * @param float $ventasNoCliente
     *
     * @return self
     */
    public function setVentasNoCliente($ventasNoCliente)
    {
        $this->ventasNoCliente = $ventasNoCliente;
        return $this;
    }

    /**
     * Get ventasNoCliente
     *
     * @return float $ventasNoCliente
     */
    public function getVentasNoCliente()
    {
        return $this->ventasNoCliente;
    }

    /**
     * Set contribucionClientes
     *
     * @param float $contribucionClientes
     *
     * @return self
     */
    public function setContribucionClientes($contribucionClientes)
    {
        $this->contribucionClientes = $contribucionClientes;
        return $this;
    }

    /**
     * Get contribucionClientes
     *
     * @return float $contribucionClientes
     */
    public function getContribucionClientes()
    {
        return round($this->contribucionClientes, 2);
    }

    /**
     * Set numeroComprasCliente
     *
     * @param int $numeroComprasCliente
     *
     * @return self
     */
    public function setNumeroComprasCliente($numeroComprasCliente)
    {
        $this->numeroComprasCliente = $numeroComprasCliente;
        return $this;
    }

    /**
     * Get numeroComprasCliente
     *
     * @return int $numeroComprasCliente
     */
    public function getNumeroComprasCliente()
    {
        return $this->numeroComprasCliente;
    }

    /**
     * Set numeroComprasNoCliente
     *
     * @param int $numeroComprasNoCliente
     *
     * @return self
     */
    public function setNumeroComprasNoCliente($numeroComprasNoCliente)
    {
        $this->numeroComprasNoCliente = $numeroComprasNoCliente;
        return $this;
    }

    /**
     * Get numeroComprasNoCliente
     *
     * @return int $numeroComprasNoCliente
     */
    public function getNumeroComprasNoCliente()
    {
        return $this->numeroComprasNoCliente;
    }

    /**
     * Set numeroClientes
     *
     * @param int $numeroClientes
     *
     * @return self
     */
    public function setNumeroClientes($numeroClientes)
    {
        $this->numeroClientes = $numeroClientes;
        return $this;
    }

    /**
     * Get numeroClientes
     *
     * @return int $numeroClientes
     */
    public function getNumeroClientes()
    {
        return $this->numeroClientes;
    }

    /**
     * Set numeroClientesQueCompran
     *
     * @param int $numeroClientesQueCompran
     *
     * @return self
     */
    public function setNumeroClientesQueCompran($numeroClientesQueCompran)
    {
        $this->numeroClientesQueCompran = $numeroClientesQueCompran;
        return $this;
    }

    /**
     * Get numeroClientesQueCompran
     *
     * @return int $numeroClientesQueCompran
     */
    public function getNumeroClientesQueCompran()
    {
        return $this->numeroClientesQueCompran;
    }

    /**
     * Set recenciaClientes
     *
     * @param float $recenciaClientes
     *
     * @return self
     */
    public function setRecenciaClientes($recenciaClientes)
    {
        $this->recenciaClientes = $recenciaClientes;
        return $this;
    }

    /**
     * Get recenciaClientes
     *
     * @return float $recenciaClientes
     */
    public function getRecenciaClientes()
    {
        return round($this->recenciaClientes, 2);
    }

    /**
     * Set frecuenciaClientes
     *
     * @param float $frecuenciaClientes
     *
     * @return self
     */
    public function setFrecuenciaClientes($frecuenciaClientes)
    {
        $this->frecuenciaClientes = $frecuenciaClientes;
        return $this;
    }

    /**
     * Get frecuenciaClientes
     *
     * @return float $frecuenciaClientes
     */
    public function getFrecuenciaClientes()
    {
        return round($this->frecuenciaClientes, 2);
    }

    /**
     * Set ticketClientes
     *
     * @param float $ticketClientes
     *
     * @return self
     */
    public function setTicketClientes($ticketClientes)
    {
        $this->ticketClientes = $ticketClientes;
        return $this;
    }

    /**
     * Get ticketClientes
     *
     * @return float $ticketClientes
     */
    public function getTicketClientes()
    {
        return round($this->ticketClientes, 2);
    }


    /**
     * Set id
     *
     * @param custom_id $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set amplitudClientes1
     *
     * @param float $amplitudClientes1
     *
     * @return self
     */
    public function setAmplitudClientes1($amplitudClientes1)
    {
        $this->amplitudClientes1 = $amplitudClientes1;
        return $this;
    }

    /**
     * Get amplitudClientes1
     *
     * @return float $amplitudClientes1
     */
    public function getAmplitudClientes1()
    {
        return round($this->amplitudClientes1, 2);
    }

    /**
     * Set amplitudClientes2
     *
     * @param float $amplitudClientes2
     *
     * @return self
     */
    public function setAmplitudClientes2($amplitudClientes2)
    {
        $this->amplitudClientes2 = $amplitudClientes2;
        return $this;
    }

    /**
     * Get amplitudClientes2
     *
     * @return float $amplitudClientes2
     */
    public function getAmplitudClientes2()
    {
        return round($this->amplitudClientes2, 2);
    }

    /**
     * Set amplitudClientes3
     *
     * @param float $amplitudClientes3
     *
     * @return self
     */
    public function setAmplitudClientes3($amplitudClientes3)
    {
        $this->amplitudClientes3 = $amplitudClientes3;
        return $this;
    }

    /**
     * Get amplitudClientes3
     *
     * @return float $amplitudClientes3
     */
    public function getAmplitudClientes3()
    {
        return round($this->amplitudClientes3, 2);
    }
}
