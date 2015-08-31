<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Model\Abstracts\InstanciaComunicacionAbstract;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;

/**
 * InstanciaComunicacion
 *
 * @ORM\Table(name="instancia_comunicacion")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository")
 */
class InstanciaComunicacion extends InstanciaComunicacionAbstract
{
    const FASE_CONFIGURACION = 'cfg';
    const FASE_NEGOCIACION   = 'ngc';
    const FASE_SIMULACION    = 'sim';
    const FASE_CIERRE        = 'cie';
    const FASE_GENERACION    = 'gen';
    const FASE_CONFIRMACION  = 'cnf';
    const FASE_FINALIZADA    = 'fin';
    const FASE_CANCELADA     = 'can';
    const PASO_1             = 1;
    const PASO_2             = 2;

    /** @var  ArrayCollection */
    private $genericas;

    /** @var  ArrayCollection */
    private $segmentadas;

    public function __construct()
    {
        $this->numPromociones = new ArrayCollection();
    }

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fec_creacion", type="datetime", nullable=true)
     */
    private $fecCreacion;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fec_ejecucion", type="datetime", nullable=true)
     */
    private $fecEjecucion;

    /**
     * @var \RM\ComunicacionBundle\Entity\Fases
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Fases")
     * @ORM\JoinColumn(name="fase", referencedColumnName="id_fase")
     */
    private $fase;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_instancia", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idInstancia;

    /**
     * @var \RM\ComunicacionBundle\Entity\SegmentoComunicacion
     *
     * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\SegmentoComunicacion")
     * @ORM\JoinColumn(name="id_segmento_comunicacion", referencedColumnName="id_segmento_comunicacion")
     *
     */
    private $idSegmentoComunicacion;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RM\ProductoBundle\Entity\NumPromociones", mappedBy="idInstancia")
     */
    private $numPromociones;

    /**
     * @var integer
     * @ORM\Column(name="paso", type="integer", nullable=false)
     */
    private $paso;


    /**
     * Set fecCreacion
     *
     * @param \Datetime $fecCreacion
     *
     * @return InstanciaComunicacion
     */
    public function setFecCreacion($fecCreacion)
    {
        $this->fecCreacion = $fecCreacion;

        return $this;
    }

    /**
     * Get fecCreacion
     *
     * @return \Datetime
     */
    public function getFecCreacion()
    {
        return $this->fecCreacion;
    }

    /**
     * Set fecEjecucion
     *
     * @param \Datetime $fecEjecucion
     *
     * @return InstanciaComunicacion
     */
    public function setFecEjecucion($fecEjecucion)
    {
        $this->fecEjecucion = $fecEjecucion;

        return $this;
    }

    /**
     * Get fecEjecucion
     *
     * @return \Datetime
     */
    public function getFecEjecucion()
    {
        return $this->fecEjecucion;
    }

    /**
     * Set fase
     *
     * @param \RM\ComunicacionBundle\Entity\Fases $fase
     *
     * @return InstanciaComunicacion
     */
    public function setFase($fase)
    {
        $this->fase = $fase;

        return $this;
    }

    /**
     * Get fase
     *
     * @return \RM\ComunicacionBundle\Entity\Fases
     */
    public function getFase()
    {
        return $this->fase;
    }

    /**
     * Set estado
     *
     * @param int $estado
     *
     * @return InstanciaComunicacion
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
     * Get idInstancia
     *
     * @return integer
     */
    public function getIdInstancia()
    {
        return $this->idInstancia;
    }

    /**
     * Set idSegmentoComunicacion
     *
     * @param SegmentoComunicacion $idSegmentoComunicacion
     *
     * @return InstanciaComunicacion
     */
    public function setIdSegmentoComunicacion(SegmentoComunicacion $idSegmentoComunicacion = null)
    {
        $this->idSegmentoComunicacion = $idSegmentoComunicacion;

        return $this;
    }

    /**
     * Get idSegmentoComunicacion
     *
     * @return SegmentoComunicacion
     */
    public function getIdSegmentoComunicacion()
    {
        return $this->idSegmentoComunicacion;
    }

    /**
     * Add numPromociones
     *
     * @param NumPromociones $numPromociones
     *
     * @return InstanciaComunicacion
     */
    public function addNumPromocion(NumPromociones $numPromociones)
    {
        $numPromociones->setIdInstancia(this);
        $this->numPromociones[] = $numPromociones;

        return $this;
    }

    /**
     * Remove numPromociones
     *
     * @param NumPromociones $numPromociones
     */
    public function removeNumPromocion(NumPromociones $numPromociones)
    {
        $this->numPromociones->removeElement($numPromociones);
    }

    /**
     * Get numPromociones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNumPromociones()
    {
        return $this->numPromociones;
    }


    /**
     * Set paso
     *
     * @param integer $paso
     *
     * @return InstanciaComunicacion
     */
    public function setPaso($paso)
    {
        $this->paso = $paso;

        return $this;
    }

    /**
     * Get paso
     *
     * @return integer
     */
    public function getPaso()
    {
        return $this->paso;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGruposSlots()
    {
        return $this
            ->getIdSegmentoComunicacion()
            ->getIdComunicacion()
            ->getPlantilla()
            ->getGruposSlots();
    }

    /**
     * @return ArrayCollection
     */
    public function getPromocionesSegmentadas()
    {
        if (!$this->segmentadas->isEmpty()) {
            $segmentadas = [];
            foreach ($this->numPromociones as $numPromocion) {
                $segmentadas = array_merge(
                    $segmentadas,
                    $numPromocion
                        ->getSegmentadas()
                        ->toArray()
                );
            }

            $this->segmentadas = new ArrayCollection($segmentadas);
        }

        return $this->segmentadas;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenericas()
    {
        if (!$this->genericas->isEmpty()) {
            $genericas = [];
            foreach ($this->numPromociones as $numPromocion) {
                $genericas = array_merge(
                    $genericas,
                    $numPromocion
                        ->getGenericas()
                        ->toArray()
                );
            }

            $this->genericas = new ArrayCollection($genericas);
        }

        return $this->genericas;

    }

    /**
     * @param GrupoSlots $grupoSlot
     *
     * @return \Doctrine\Common\Collections\Collection|static
     */
    public function getNumPromocionesByGrupo(GrupoSlots $grupoSlot)
    {
        return $this
            ->numPromociones
            ->filter(
                function (NumPromociones $numPromocion) use ($grupoSlot) {
                    return $numPromocion->getIdGrupo()->getIdGrupo() === $grupoSlot->getIdGrupo();
                }
            );

    }


}