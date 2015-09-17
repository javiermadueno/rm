<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Entity\Canal;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Plantilla
 *
 * @ORM\Table(name="plantilla")
 * @ORM\Entity(repositoryClass="RM\PlantillaBundle\Entity\PlantillaRepository")
 */
class Plantilla implements PlantillaInterface
{

    public function __construct()
    {
        $this->gruposSlots = new ArrayCollection();
        $this->comunicaciones = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id_plantilla", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlantilla;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var Canal
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Canal")
     * @ORM\JoinColumn(name="id_canal", referencedColumnName="id_canal")
     */
    private $canal;

    /**
     * @var integer
     *
     * @ORM\Column(name="lienzo_ancho", type="integer", nullable=true)
     */
    private $lienzoAncho;

    /**
     * @var integer
     *
     * @ORM\Column(name="lienzo_alto", type="integer", nullable=true)
     */
    private $lienzoAlto;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RM\PlantillaBundle\Entity\GrupoSlots", mappedBy="idPlantilla")
     */
    private $gruposSlots;

    /**
     * @var string
     * @ORM\Column(name="descripcion", nullable=true)
     */
    private $descripcion;

    /**
     * @var bool
     * @ORM\Column(name="es_modelo", type="boolean", options={"default"=false})
     */
    private $esModelo;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RM\ComunicacionBundle\Entity\Comunicacion", mappedBy="plantilla")
     */
    private $comunicaciones;

    /**
     * @var bool
     */
    private $editable = null;




    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Plantilla
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
     * Set lienzoAncho
     *
     * @param integer $lienzoAncho
     * @return Plantilla
     */
    public function setLienzoAncho($lienzoAncho)
    {
        $this->lienzoAncho = $lienzoAncho;
    
        return $this;
    }

    /**
     * Get lienzoAncho
     *
     * @return integer 
     */
    public function getLienzoAncho()
    {
        return $this->lienzoAncho;
    }

    /**
     * Set lienzoAlto
     *
     * @param integer $lienzoAlto
     * @return Plantilla
     */
    public function setLienzoAlto($lienzoAlto)
    {
        $this->lienzoAlto = $lienzoAlto;
    
        return $this;
    }

    /**
     * Get lienzoAlto
     *
     * @return integer 
     */
    public function getLienzoAlto()
    {
        return $this->lienzoAlto;
    }

    /**
     * Set estado
     *
     * @param int $estado
     * @return Plantilla
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
     * Get idPlantilla
     *
     * @return integer 
     */
    public function getIdPlantilla()
    {
        return $this->idPlantilla;
    }

    /**
     * Add gruposSlots
     *
     * @param GrupoSlots $gruposSlots
     * @return Plantilla
     */
    public function addGruposSlot(GrupoSlots $gruposSlots)
    {
        $this->gruposSlots[] = $gruposSlots;
    
        return $this;
    }

    /**
     * Remove gruposSlots
     *
     * @param GrupoSlots $gruposSlots
     */
    public function removeGruposSlot(GrupoSlots $gruposSlots)
    {
        $this->gruposSlots->removeElement($gruposSlots);
    }

    /**
     * Get gruposSlots
     *
     * @return Collection
     */
    public function getGruposSlots()
    {
        $criteria = Criteria::create();
        $criteria->orderBy( ['idGrupo' => Criteria::DESC]);

        $grupos = $this->gruposSlots->filter(function(GrupoSlots $grupo){
                return $grupo->getEstado() > -1;
            })->matching($criteria);

        return $grupos;

    }

    /**
     * Set canal
     *
     * @param Canal $canal
     * @return Plantilla
     */
    public function setCanal(Canal $canal = null)
    {
        $this->canal = $canal;
    
        return $this;
    }

    /**
     * Get canal
     *
     * @return Canal
     */
    public function getCanal()
    {
        return $this->canal;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Plantilla
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set esModelo
     *
     * @param boolean $esModelo
     * @return Plantilla
     */
    public function setEsModelo($esModelo)
    {
        $this->esModelo = $esModelo;
    
        return $this;
    }

    /**
     * Get esModelo
     *
     * @return boolean 
     */
    public function getEsModelo()
    {
        return $this->esModelo;
    }

    /**
     * @return ArrayCollection
     */
    public function getComunicaciones()
    {
        return $this->comunicaciones;
    }

    /**
     * @return bool
     */
    public function getEditable()
    {
        $comunicaciones_generadas = $this->comunicaciones
            ->filter(
                function(Comunicacion $comunicacion) {
                    return true === $comunicacion->getGenerada();
                }
            );

        return $comunicaciones_generadas->isEmpty();
    }
}