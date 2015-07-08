<?php

namespace RM\ComunicacionBundle\Entity;

use Cron\CronExpression;
use RM\SegmentoBundle\Entity\Segmento;
use Doctrine\ORM\Mapping as ORM;
use RM\ComunicacionBundle\Model\Interfaces\FechaInicioFinInterface;
use RM\ComunicacionBundle\Model\Validator as AssertComunicacion;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SegmentoComunicacion
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="segmento_comunicacion")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\SegmentoComunicacionRepository")
 * @AssertComunicacion\CompruebaFechas()
 * @AssertComunicacion\FechasEntrePeriodoComunicacion()
 */
class SegmentoComunicacion implements FechaInicioFinInterface
{
    const FREC_DIARIA       =   1;
    const FREC_SEMANAL      =   2;
    const FREC_QUINCENAL    =   3;
    const FREC_MENSUAL      =   4;
    const FREC_TRIMESTRAL   =   5;
    const FREC_CUATRIMESTRAL=   6;
    const FREC_SEMESTRAL    =   8;
    const FREC_ANUAL       =    7;

	/**
	 * @var integer
	 * @ORM\Column(name="id_segmento_comunicacion", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idSegmentoComunicacion;
	
	
	/**
	 * @var \RM\ComunicacionBundle\Entity\Comunicacion
	 *
	 * @ORM\ManyToOne(targetEntity="RM\ComunicacionBundle\Entity\Comunicacion", inversedBy="segmentos")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_comunicacion", referencedColumnName="id_comunicacion")
	 * })
     *
     * @Assert\NotBlank()
	 */
	private $idComunicacion;
	
	
	/**
	 * @var \RM\SegmentoBundle\Entity\Segmento
	 * 
	 * @ORM\ManyToOne(targetEntity="RM\SegmentoBundle\Entity\Segmento")
	 * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_segmento", referencedColumnName="id_segmento")
     * })
     *
     * @Assert\NotBlank()
	 */
	private $idSegmento;
	
	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fec_inicio", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
	 */
	private $fecInicio;
	
	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fec_fin", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
	 */
	private $fecFin;
	
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="hora_prog", type="time", nullable=true)
	 */
	private $horaProg;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="tipo", type="smallint", nullable=true)
	 */
	private $tipo;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="dia", type="smallint", nullable=true)
	 */
	private $dia;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="mes", type="smallint", nullable=true)
	 */
	private $mes;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="estado", type="smallint", nullable=true, options={"default" = 0})
	 */
	private $estado = 1;

    /**
     * @var \Datetime
     * @ORM\Column(name="proxima_ejecucion", type="datetime", nullable=true)
     */
	private $proximaEjecucion;
	
	
	
	/**
	 * Get idSegmentoComunicacion
	 *
	 * @return integer
	 */
	public function getIdSegmentoComunicacion()
	{
		return $this->idSegmentoComunicacion;
	}	
	
	/**
	 * Set idComunicacion
	 *
	 * @param Comunicacion $idComunicacion
	 * @return SegmentoComunicacion
	 */
	public function setIdComunicacion(Comunicacion $idComunicacion = null)
	{
		$this->idComunicacion = $idComunicacion;
	
		return $this;
	}
	
	
	/**
	 * Get idComunicacion
	 *
	 * @return Comunicacion
	 */
	public function getIdComunicacion()
	{
		return $this->idComunicacion;
	}
	
	/**
	 * Set idSegmento
	 *
	 * @param Segmento $idSegmento
	 * @return SegmentoComunicacion
	 */
	public function setIdSegmento(Segmento $idSegmento = null)
	{
		$this->idSegmento = $idSegmento;
	
		return $this;
	}
	
	/**
	 * Get idSegmento
	 *
	 * @return \RM\SegmentoBundle\Entity\Segmento
	 */
	public function getIdSegmento()
	{
		return $this->idSegmento;
	}
	
	/**
	 * Set fecInicio
	 *
	 * @param \Datetime $fecInicio
	 * @return SegmentoComunicacion
	 */
	public function setFecInicio($fecInicio = null)
	{
		$this->fecInicio = $fecInicio;
	
		return $this;
	}
	
	/**
	 * Get fecInicio
	 *
	 * @return \Datetime
	 */
	public function getFecInicio()
	{
		return $this->fecInicio;
	}
	
	/**
	 * Set fecFin
	 *
	 * @param \Datetime $fecFin
	 * @return SegmentoComunicacion
	 */
	public function setFecFin($fecFin = null)
	{
		$this->fecFin = $fecFin;
	
		return $this;
	}
	
	/**
	 * Get fecFin
	 *
	 * @return \Datetime
	 */
	public function getFecFin()
	{
		return $this->fecFin;
	}	
	
	/**
	 * Set horaProg
	 *
	 * @param \Datetime $horaProg
	 * @return SegmentoComunicacion
	 */
	public function setHoraProg($horaProg)
	{
		$this->horaProg = $horaProg;
	
		return $this;
	}
	
	/**
	 * Get horaProg
	 *
	 * @return \Datetime
	 */
	public function getHoraProg()
	{
		return $this->horaProg;
	}
	
	/**
	 * Set tipo
	 *
	 * @param int $tipo
	 * @return SegmentoComunicacion
	 */
	public function setTipo($tipo)
	{
		$this->tipo = $tipo;
	
		return $this;
	}
	
	/**
	 * Get tipo
	 *
	 * @return int
	 */
	public function getTipo()
	{
		return $this->tipo;
	}
	
	/**
	 * Set dia
	 *
	 * @param int $dia
	 * @return SegmentoComunicacion
	 */
	public function setDia($dia)
	{
		$this->dia = $dia;
	
		return $this;
	}
	
	/**
	 * Get dia
	 *
	 * @return int
	 */
	public function getDia()
	{
		return $this->dia;
	}	
	
	/**
	 * Set mes
	 *
	 * @param int $mes
	 * @return SegmentoComunicacion
	 */
	public function setMes($mes)
	{
		$this->mes = $mes;
	
		return $this;
	}
	
	/**
	 * Get mes
	 *
	 * @return int
	 */
	public function getMes()
	{
		return $this->mes;
	}
	
	/**
	 * Set estado
	 *
	 * @param int $estado
	 * @return SegmentoComunicacion
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
     * Set proximaEjecucion
     *
     * @param \DateTime $proximaEjecucion
     * @return SegmentoComunicacion
     */
    public function setProximaEjecucion($proximaEjecucion)
    {
        $this->proximaEjecucion = $proximaEjecucion;
    
        return $this;
    }

    /**
     * Get proximaEjecucion
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @ORM\PostLoad()
     * @return \DateTime | null
     */
    public function getProximaEjecucion()
    {
        $ahora = new \DateTime('now');
        //Todavia no empieza la comunicacion

         if(!$this->proximaEjecucion && $this->fecInicio > $ahora){
             $this->proximaEjecucion = $this->fecInicio;
             $this->proximaEjecucion->setTime($this->horaProg->format('H'), $this->horaProg->format('i'));
             return $this->proximaEjecucion;
        }

        //Ha terminado la comunicacion
        if($ahora > $this->fecFin){
            $this->proximaEjecucion = null;
            $this->getEstado() == Comunicacion::ESTADO_ELIMINADO ?:
                $this->setEstado(Comunicacion::ESTADO_COMPLETADA);
            return $this->proximaEjecucion;
        }

        //Si no hay una fecha calculada se calcula con respecto a la fecha actual
        if(!$this->proximaEjecucion){
            $this->calculaProximaEjecucion();
        }

        return $this->proximaEjecucion;
    }

    /**
     * @return \DateTime
     */
    public function calculaProximaEjecucion()
    {
        $cronExpresion = '';
        $minutos = $this->horaProg->format('i');
        $hora = $this->horaProg->format('H');
        $dia = $this->dia;
        $mes = $this->mes;

        /**
         *
         *      *    *    *    *    *    *
         *      -    -    -    -    -    -
         *      |    |    |    |    |    |
         *      |    |    |    |    |    + year [optional]
         *      |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
         *      |    |    |    +---------- month (1 - 12)
         *      |    |    +--------------- day of month (1 - 31)
         *      |    +-------------------- hour (0 - 23)
         *      +------------------------- min (0 - 59)
         *
         */
        switch($this->tipo)
        {
            case self::FREC_DIARIA: //Diaria
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), '*/1', '*', '*');
                break;
            case self::FREC_SEMANAL: //Semanal
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), '?', '*', $dia);
                break;
            case self::FREC_QUINCENAL: //Quincenal
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), '*/15', '*', '*');
                break;
            case self::FREC_MENSUAL: //Mensual
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), intval($dia), '*/1', '*');
                break;
            case self::FREC_TRIMESTRAL: //Trimestral
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), intval($dia), '*/3', '*');
                break;
            case self::FREC_CUATRIMESTRAL: //Cuatrimestral
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), intval($dia), '*/4', '*');
                break;
            case self::FREC_SEMESTRAL: //Semestral
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), intval($dia), '*/6', '*');
                break;
            case self::FREC_ANUAL: //Anual
                $cronExpresion = sprintf('%s %s %s %s %s', intval($minutos), intval($hora), intval($dia), intval($mes), '*');
                break;
        }

        $cron = CronExpression::factory($cronExpresion);
        $this->proximaEjecucion = $cron->getNextRunDate(new \DateTime('now'));
    }
}