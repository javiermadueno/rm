<?php

namespace RM\TrackingBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Mongo\Document(collection="tracking", repositoryClass="RM\InsightBundle\Document\TrackingRepository")
 */
class TrackingEmail
{
    const OPEN        = 'email.open';
    const CLICK       = 'email.click';
    const UNSUBSCRIBE = 'email.unsubscribe';

    /**
     * @Mongo\Id()
     */
    private $id;

    /**
     * @Mongo\String(name="tipo")
     */
    private $tipo;

    /**
     * @Mongo\Int(name="instancia")
     */
    private $instancia;

    /**
     * @Mongo\Int(name="cliente")
     */
    private $cliente;

    /**
     * @Mongo\Int(name="producto")
     */
    private $producto;

    /**
     * @Mongo\Int(name="promocion")
     */
    private $promocion;

    /**
     * @Mongo\Date(name="fecha")
     */
    private $fecha;

    /**
     * @Mongo\Collection(name="time_bucket")
     */
    private $time_bucket;

    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param $cliente
     *
     * @return $this
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeBucket()
    {
        return $this->time_bucket;
    }

    /**
     * @param mixed $time_bucket
     */
    public function setTimeBucket($time_bucket)
    {
        $this->time_bucket = $time_bucket;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param $fecha
     *
     * @return $this
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstancia()
    {
        return $this->instancia;
    }

    /**
     * @param $instancia
     *
     * @return $this
     */
    public function setInstancia($instancia)
    {
        $this->instancia = $instancia;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param $tipo
     *
     * @return $this
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * @param $producto
     *
     * @return $this
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPromocion()
    {
        return $this->promocion;
    }

    /**
     * @param $promocion
     *
     * @return $this
     */
    public function setPromocion($promocion)
    {
        $this->promocion = $promocion;
        return $this;
    }



    public function calculateTimeBucketing()
    {
        if (!$this->fecha instanceof \DateTime) {
            return $this;
        }

        $hora = $this->fecha->format('Y-m-d H');
        $dia  = $this->fecha->format('Y-m-d');
        $mes  = $this->fecha->format('Y-m');
        $week = $this->fecha->format('Y-W');
        $year = $this->fecha->format('Y');


        $bucketing[] = sprintf('%s-hour', $hora);
        $bucketing[] = sprintf('%s-day', $dia);
        $bucketing[] = sprintf('%s-month', $mes);
        $bucketing[] = sprintf('%s-week', $week);
        $bucketing[] = sprintf('%s-year', $year);

        $this->setTimeBucket($bucketing);

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return null|TrackingEmail
     */
    public static function createFromRequest(Request $request)
    {
        $self = new self();

        $instancia = $request->query->getInt('instancia', null);
        $cliente   = $request->query->getInt('cliente', null);

        if(is_null($instancia) || !is_numeric($instancia)) {
            return null;
        }

        $self
            ->setCliente($cliente)
            ->setInstancia($instancia)
            ->setFecha(new \DateTime())
            ->calculateTimeBucketing();

        return $self;

    }

    /**
     * @param Request $request
     *
     * @return null|TrackingEmail
     */
    public static function createApertureEventFromRequest(Request $request)
    {
        $tracking = self::createFromRequest($request);

        if(!$tracking instanceof TrackingEmail) {
            return null;
        }

        $tracking->setTipo(self::OPEN);

        return $tracking;

    }

    /**
     * @param Request $request
     *
     * @return null|TrackingEmail
     */
    public static function createClickEventFromRequest(Request $request)
    {
        $tracking = self::createFromRequest($request);

        if(!$tracking instanceof TrackingEmail) {
            return null;
        }

        $producto  = $request->query->getInt('producto', null);
        $promocion = $request->query->getInt('promocion', null);

        $tracking
            ->setProducto($producto)
            ->setPromocion($promocion)
            ->setTipo(self::CLICK)
        ;

        return $tracking;
    }


}