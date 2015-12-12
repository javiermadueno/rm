<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Model\Abstracts\InstanciaComunicacionAbstract;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;

/**
 * InstanciaComunicacion
 *
 * @ORM\Table(name="instancia_comunicacion")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository")
 */
class InstanciaComunicacion extends InstanciaComunicacionAbstract
{
    const FASE_CONFIGURACION = 'cfg';
    const FASE_NEGOCIACION = 'ngc';
    const FASE_SIMULACION = 'sim';
    const FASE_CIERRE = 'cie';
    const FASE_GENERACION = 'gen';
    const FASE_CONFIRMACION = 'cnf';
    const FASE_FINALIZADA = 'fin';
    const FASE_CANCELADA = 'can';
    const PASO_1 = 1;
    const PASO_2 = 2;

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
     * @var int
     * @ORM\Column(name="num_enviadas", type="integer", nullable=true)
     */
    private $numComunicaciones;

    /**
     * @var bool
     * @ORM\Column(name="enviada", type="boolean", nullable=true, options={"default"=false})
     */
    private $enviada;

    /**
     * @var string
     * @ORM\Column(name="asunto", type="string", length=500, nullable=true)
     */
    private $asunto;

    /**
     * @var \DateTime
     * @ORM\Column(name="fecha_envio", type="datetime", nullable=true)
     */
    private $fechaEnvio;

    /**
     * @var bool
     * @ORM\Column(name="envio_inmediato", type="boolean", nullable=true, options={"default"=false})
     */
    private $envioInmediato;

    /**
     * @return boolean
     */
    public function isEnvioInmediato()
    {
        return $this->envioInmediato;
    }

    /**
     * @param boolean $envioInmediato
     */
    public function setEnvioInmediato($envioInmediato)
    {
        $this->envioInmediato = $envioInmediato;
    }

    /**
     * @return \DateTime
     */
    public function getFechaEnvio()
    {
        return $this->fechaEnvio;
    }

    /**
     * @param \DateTime $fechaEnvio
     */
    public function setFechaEnvio($fechaEnvio)
    {
        $this->fechaEnvio = $fechaEnvio;
    }



    /**
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * @param string $asunto
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }

    /**
     * @return boolean
     */
    public function isEnviada()
    {
        return $this->enviada;
    }

    /**
     * @param boolean $enviada
     */
    public function setEnviada($enviada)
    {
        $this->enviada = $enviada;
    }




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
        $numPromociones->setIdInstancia($this);
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
     * @return int
     */
    public function getNumComunicaciones()
    {
        return $this->numComunicaciones;
    }

    /**
     * @param int $numComunicaciones
     *
     * @return $this
     */
    public function setNumComunicaciones($numComunicaciones = 0)
    {
        $this->numComunicaciones = $numComunicaciones;
        return $this;
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
        if (is_null($this->segmentadas)  || $this->segmentadas->isEmpty()) {
            $segmentadas = [];

            /** @var NumPromociones $numPromocion */
            foreach ($this->numPromociones as $numPromocion) {
                $segmentadas = array_merge(
                    $segmentadas,
                    $numPromocion
                        ->getPromocionesSegmentadas()
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
    public function getPromocionesGenericas()
    {
        if (is_null($this->genericas) || $this->genericas->isEmpty()) {
            $genericas = [];

            /** @var NumPromociones $numPromocion */
            foreach ($this->numPromociones as $numPromocion) {
                $genericas = array_merge(
                    $genericas,
                    $numPromocion
                        ->getPromocionesGenericas()
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

    /**
     * @return bool
     */
    public function isTodosGruposRellenos()
    {
        foreach ($this->getGruposSlots() as $grupo) {
            if ($this->getNumPromocionesByGrupo($grupo)->count() <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isTodasGenericasDefinidas()
    {
        /** @var GrupoSlots $grupo */
        foreach ($this->getGruposSlots() as $grupo) {

            $totalGenericas =
                array_sum(
                    array_map(
                        function(NumPromociones $numPro){
                            return (int) $numPro->getNumGenericas();
                        },
                        $this->getNumPromocionesByGrupo($grupo)->toArray()
                    )
                );

            if($totalGenericas < $grupo->getNumSlots()) {
                return false;
            }

        }

        return true;

    }

    /**
     * @return bool
     */
    public function isTodasGenericasCreadas()
    {
        /** @var NumPromociones $numPromocion */
        foreach ($this->getNumPromociones() as $numPromocion) {
            if (!$numPromocion->isGenericasCompletas()) {
                return false;
            }
        }

        return true;

    }

    /**
     * @return bool
     */
    public function isTodasSegmentadasCreadas()
    {
        /** @var NumPromociones $numPromocion */
        foreach ($this->getNumPromociones() as $numPromocion) {
            if (!$numPromocion->isSegementadasCompletas()) {
                return false;
            }
        }

        return true;

    }

    /**
     * @return bool
     */
    public function isTotalGenericasMayorQueNumeroSlot()
    {
        foreach ($this->getGruposSlots() as $grupo) {
            $totalGenericas =
                array_sum(
                    array_map(
                        function(NumPromociones $numPro){
                            return (int) $numPro->getPromocionesGenericas()->count();
                        },
                        $this->getNumPromocionesByGrupo($grupo)->toArray()
                    )
                );

            if($totalGenericas < $grupo->getNumSlots()) {
                return false;
            }
        }

        return true;


    }

    /**
     * @return bool
     */
    public function isNingunaPromocionPendiente()
    {
        /** @var NumPromociones $numPro */
        foreach ($this->getNumPromociones() as $numPro) {
           if (false === $numPro->isNingunaPromocionPendiente()) {
               return false;
           }
        }

        return true;

    }

    /**
     * @return number
     */
    public function getTotalPromocionesAceptadas()
    {
        return $this->getTotalPromocionesByEstado(Promocion::ACEPTADA);
    }

    /**
     * @return number
     */
    public function getTotalPromocionesRechazadas()
    {
        return $this->getTotalPromocionesByEstado(Promocion::RECHAZADA);
    }

    /**
     * @return number
     */
    public function getTotalPromocionesPendientes()
    {
        return $this->getTotalPromocionesByEstado(Promocion::PENDIENTE);
    }

    /**
     * @param $estado
     *
     * @return number
     */
    public function getTotalPromocionesByEstado($estado)
    {
        $total = array_sum(
            array_map(
                function(NumPromociones $numPro) use ($estado){
                    return $numPro->getPromocionesByEstado($estado);
                },
                $this->getNumPromociones()->toArray()
            )
        );

        return $total;

    }




}