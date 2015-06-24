<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NumPromociones
 *
 * @ORM\Table(name="num_promociones")
 * @ORM\Entity(repositoryClass="RM\ProductoBundle\Entity\NumPromocionesRepository")
 */
class NumPromociones
{

    public function __construct()
    {

        $this->promociones = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="num_segmentadas", type="integer", nullable=true)
     */
    private $numSegmentadas;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_genericas", type="integer", nullable=true)
     */
    private $numGenericas;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_num_pro", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idNumPro;

    /**
     * @var \RM\ComunicacionBundle\Entity\InstanciaComunicacion
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\InstanciaComunicacion", inversedBy="numPromociones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_instancia", referencedColumnName="id_instancia")
     * })
     */
    private $idInstancia;

    /**
     * @var \RM\PlantillaBundle\Entity\GrupoSlots
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\GrupoSlots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo")
     * })
     */
    private $idGrupo;

    /**
     * @var \RM\CategoriaBundle\Entity\Categoria
     *
     * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id_categoria")
     * })
     */
    private $idCategoria;

    /**
     * @var \RM\ProductoBundle\Entity\Promocion
     * @ORM\OneToMany(targetEntity="RM\ProductoBundle\Entity\Promocion", mappedBy="numPromocion")
     *
     */
    private $promociones;


    /**
     * Set numSegmentadas
     *
     * @param integer $numSegmentadas
     *
     * @return NumPromociones
     */
    public function setNumSegmentadas($numSegmentadas)
    {
        $this->numSegmentadas = $numSegmentadas;

        return $this;
    }

    /**
     * Get numSegmentadas
     *
     * @return integer
     */
    public function getNumSegmentadas()
    {
        return $this->numSegmentadas;
    }

    /**
     * Set numGenericas
     *
     * @param integer $numGenericas
     *
     * @return NumPromociones
     */
    public function setNumGenericas($numGenericas)
    {
        $this->numGenericas = $numGenericas;

        return $this;
    }

    /**
     * Get numGenericas
     *
     * @return integer
     */
    public function getNumGenericas()
    {
        return $this->numGenericas;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
     *
     * @return NumPromociones
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return smallint
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Get idNumPro
     *
     * @return integer
     */
    public function getIdNumPro()
    {
        return $this->idNumPro;
    }

    /**
     * Set idInstancia
     *
     * @param \RM\ComunicacionBundle\Entity\InstanciaComunicacion $idInstancia
     *
     * @return NumPromociones
     */
    public function setIdInstancia(\RM\ComunicacionBundle\Entity\InstanciaComunicacion $idInstancia = null)
    {
        $this->idInstancia = $idInstancia;

        return $this;
    }

    /**
     * Get idInstancia
     *
     * @return \RM\ComunicacionBundle\Entity\InstanciaComunicacion
     */
    public function getIdInstancia()
    {
        return $this->idInstancia;
    }

    /**
     * Set idGrupo
     *
     * @param \RM\PlantillaBundle\Entity\GrupoSlots $idGrupo
     *
     * @return NumPromociones
     */
    public function setIdGrupo(\RM\PlantillaBundle\Entity\GrupoSlots $idGrupo = null)
    {
        $this->idGrupo = $idGrupo;

        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return \RM\PlantillaBundle\Entity\GrupoSlots
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set idCategoria
     *
     * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria
     *
     * @return NumPromociones
     */
    public function setIdCategoria(\RM\CategoriaBundle\Entity\Categoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;

        return $this;
    }

    /**
     * Get idCategoria
     *
     * @return \RM\CategoriaBundle\Entity\Categoria
     */
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /**
     * Add promociones
     *
     * @param \RM\ProductoBundle\Entity\Promocion $promociones
     *
     * @return NumPromociones
     */
    public function addPromocion(\RM\ProductoBundle\Entity\Promocion $promociones)
    {
        $this->promociones[] = $promociones;

        return $this;
    }

    /**
     * Remove promociones
     *
     * @param \RM\ProductoBundle\Entity\Promocion $promociones
     */
    public function removePromocion(\RM\ProductoBundle\Entity\Promocion $promociones)
    {
        $this->promociones->removeElement($promociones);
    }

    /**
     * Get promociones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromociones()
    {
        return $this->promociones->filter(function (Promocion $promocion) {
            return $promocion->getEstado() > -1;
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromocionesSegentadas()
    {

        return $this->promociones->filter(function (Promocion $promocion) {
            return $promocion->getTipo() == Promocion::TIPO_SEGMENTADA
            & $promocion->getEstado() > -1;
        });

    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromocionesGenericas()
    {

        return $this->promociones->filter(function (Promocion $promocion) {
            return $promocion->getTipo() == Promocion::TIPO_GENERICA
            & $promocion->getEstado() > -1;
        });

    }
}