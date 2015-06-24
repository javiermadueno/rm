<?php

namespace RM\CategoriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NivelCategoria
 *
 * @ORM\Table(name="nivel_categoria")
 * @ORM\Entity
 */
class NivelCategoria
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_nivel_categoria", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idNivelCategoria;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;


    /**
     * @var samllint
     *
     * @ORM\Column(name="asociado", type="boolean", nullable=false)
     */
    private $asociado;

    /**
     * Get idNivelCategoria
     *
     * @return integer
     */

    public function getIdNivelCategoria()
    {
        return $this->idNivelCategoria;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return NivelCategoria
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
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
     * @return samllint
     */
    public function getAsociado()
    {
        return $this->asociado;
    }

    /**
     * @param samllint $asociado
     *
     * @return NivelCategoria
     */
    public function setAsociado($asociado)
    {
        $this->asociado = $asociado;

        return $this;
    }
}
