<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Canal
 *
 * @ORM\Table(name="canal")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\CanalRepository")
 */
class Canal
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_canal", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCanal;

    /**
     * Get idCanal
     *
     * @return integer
     */
    public function getIdCanal()
    {
        return $this->idCanal;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Canal
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }
}