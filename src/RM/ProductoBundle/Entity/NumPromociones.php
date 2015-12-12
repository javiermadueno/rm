<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\CategoriaBundle\Entity\Categoria;

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
     * @Assert\GreaterThanOrEqual( value = 0)
     */
    private $numSegmentadas;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_genericas", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(value = 0)
     */
    private $numGenericas;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true, options={"default" = 1})
     */
    private $estado = 1;

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
     * @Assert\NotBlank()
     */
    private $idInstancia;

    /**
     * @var \RM\PlantillaBundle\Entity\GrupoSlots
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\GrupoSlots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo")
     * })
     * @Assert\NotBlank()
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
     * @ORM\OneToMany(targetEntity="RM\ProductoBundle\Entity\Promocion", mappedBy="numPromocion", indexBy="idPromocion")
     *
     */
    private $promociones;


    private $nombre = '';

    public function getNombre()
    {
        if($this->idCategoria instanceof Categoria) {
            $this->nombre = $this->idCategoria->getNombre();
        }

        return $this->nombre;
    }





    /**
     * Set numSegmentadas
     *
     * @param integer $numSegmentadas
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
     * @param int $estado
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
     * @return int
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
     * @param InstanciaComunicacion $idInstancia
     * @return NumPromociones
     */
    public function setIdInstancia(InstanciaComunicacion $idInstancia = null)
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
     * @param GrupoSlots $idGrupo
     * @return NumPromociones
     */
    public function setIdGrupo(GrupoSlots $idGrupo = null)
    {
        $this->idGrupo = $idGrupo;
    
        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return GrupoSlots
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set idCategoria
     *
     * @param Categoria $idCategoria
     * @return NumPromociones
     */
    public function setIdCategoria(Categoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;
    
        return $this;
    }

    /**
     * Get idCategoria
     *
     * @return Categoria
     */
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /**
     * Add promociones
     *
     * @param Promocion $promociones
     * @return NumPromociones
     */
    public function addPromocion(Promocion $promociones)
    {
        $promociones->setNumPromocion($this);

        $this->promociones->add($promociones);
    
        return $this;
    }

    /**
     * Remove promociones
     *
     * @param \RM\ProductoBundle\Entity\Promocion $promociones
     */
    public function removePromocion(Promocion $promociones)
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
        return $this->promociones->filter(function (Promocion $promocion){
                return $promocion->getEstado() > -1;
            });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromocionesSegmentadas(){

      return $this->promociones->filter(function(Promocion $promocion){
        return $promocion->getTipo() == Promocion::TIPO_SEGMENTADA
              && $promocion->getEstado() > -1;
      });

    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromocionesGenericas(){

      return $this->promociones->filter(function(Promocion $promocion){
        return $promocion->getTipo() ==  Promocion::TIPO_GENERICA
            && $promocion->getEstado() > -1;
      });

    }


    /**
     * @return bool
     */
    public function isSegementadasCompletas()
    {
        return $this->getPromocionesSegmentadas()->count() >= $this->numSegmentadas;
    }

    /**
     * @return bool
     */
    public function isGenericasCompletas()
    {
        return $this->getPromocionesGenericas()->count() >= $this->numGenericas;
    }

    /**
     * @return int
     */
    public function getTotalPromociones()
    {
        return
            (int) $this->numSegmentadas + $this->numGenericas;
    }

    /**
     * @return int
     */
    public function getTotalPromocionesRealizadas()
    {
        return
            (int) $this->getPromocionesGenericas()->count() + $this->getPromocionesSegmentadas()->count();
    }

    /**
     * @return bool
     */
    public function isNingunaPromocionPendiente()
    {
        /** @var Promocion $promocion */
        foreach ($this->getPromocionesSegmentadas() as $promocion) {
            if (Promocion::PENDIENTE === $promocion->getAceptada()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int
     */
    public function getTotalPromocionesAceptadas()
    {
        return $this->getPromocionesByEstado(Promocion::ACEPTADA);
    }

    /**
     * @return int
     */
    public function getTotalPromocionesPendientes()
    {
        return $this->getPromocionesByEstado(Promocion::PENDIENTE);
    }

    /**
     * @return int
     */
    public function getTotalPromocionesRechazadas()
    {
        return $this->getPromocionesByEstado(Promocion::RECHAZADA);
    }

    /**
     * @param $estado
     *
     * @return int
     */
    public function getPromocionesByEstado($estado)
    {

        $promociones = $this
            ->getPromocionesSegmentadas();

        $promociones = $promociones->filter(function(Promocion $promocion) use ($estado) {
                return $estado === $promocion->getAceptada();
            });

        return $promociones->count();
    }
}