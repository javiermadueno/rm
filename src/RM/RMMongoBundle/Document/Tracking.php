<?php

namespace RM\RMMongoBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/**
 * @Mongo\Document(collection="tracking", repositoryClass="RM\RMMongoBundle\Document\TrackingRepository")
 */
class Tracking
{
    /**
     * @Mongo\Id(strategy="NONE")
     */
    private $id;

    /**
     * @Mongo\Field(name="instancia", type="int")
     * @Mongo\UniqueIndex()
     */
    private $instancia;

    /**
     * @Mongo\Field(name="aperturas", type="int")
     */
    private $aperturas;


    /**
     * @return mixed
     */
    public function getAperturas()
    {
        return $this->aperturas;
    }

    /**
     * @param mixed $aperturas
     */
    public function setAperturas($aperturas)
    {
        $this->aperturas = $aperturas;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getInstancia()
    {
        return $this->instancia;
    }

    /**
     * @param mixed $instancia
     */
    public function setInstancia($instancia)
    {
        $this->instancia = $instancia;
    }

} 