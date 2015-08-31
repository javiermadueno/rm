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

    public function __construct(){

        $this->promociones = new ArrayCollection();
        $this->segmentadas = new ArrayCollection();
        $this->genericas   = new ArrayCollection();
    }

    protected $segmentadas;

    protected $genericas;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_segmentadas", type="integer", nullable=true)
     * @Assert\GreaterThan( value = 0)
     */
    private $numSegmentadas;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_genericas", type="integer", nullable=true)
     * @Assert\GreaterThan(value = 0)
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
        return $promocion->getTipo() === Promocion::TIPO_SEGMENTADA
              && $promocion->getEstado() > -1;
      });

    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromocionesGenericas(){

      return $this->promociones->filter(function(Promocion $promocion){
        return $promocion->getTipo() ===  Promocion::TIPO_GENERICA
            && $promocion->getEstado() > -1;
      });

    }

    public function getGenericas()
    {
        $this->genericas = $this->promociones
            ->filter(function(Promocion $promocion) {
                return
                    Promocion::TIPO_GENERICA === $promocion->getTipo()
                    &&
                    $promocion->getEstado() > -1;
            });

        $this->genericas->forAll(function($key, Promocion $promocion){
            $promocion->setNumPromocion($this);
        });

        return $this->genericas;
    }

    public function getSegmentadas()
    {
        $this->segmentadas = $this->promociones
            ->filter(function(Promocion $promocion) {
                return
                    Promocion::TIPO_SEGMENTADA === $promocion->getTipo()
                    &&
                    $promocion->getEstado() > -1;
            });

        $this->segmentadas->forAll( function($key, Promocion $promocion){
            $promocion->setNumPromocion($this);
        });

        return $this->segmentadas;
    }

    public function addSegmentadas(Promocion $promocion)
    {
        $promocion
            ->setTipo(Promocion::TIPO_SEGMENTADA)
            ->setEstado(1)
            ->setNumPromocion($this);


        $this->promociones->add($promocion);
        return $this;
    }

    public function removeSegmentadas(Promocion $promocion)
    {
        $promocion->setEstado(-1);
        $this->promociones->remove($promocion);
        return $this;
    }

    public function addGenericas(Promocion $promocion)
    {
        $promocion
            ->setTipo(Promocion::TIPO_GENERICA)
            ->setEstado(1)
            ->setNumPromocion($this);

        $this->promociones->add($promocion);

        return $this;
    }

    public function removeGenericas(Promocion $promocion)
    {
        $promocion->setEstado(-1);
        $this->promociones->remove($promocion);

        return $this;
    }

    public function isSegementadasCompletas()
    {
        return $this->getSegmentadas()->count() >= $this->numSegmentadas;
    }

    public function isGenericasCompletas()
    {
        return $this->getGenericas()->count() >= $this->numGenericas;
    }
}