<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;


/**
 * GrupoSlots
 *
 * @ORM\Table(name="grupo_slots")
 * @ORM\Entity(repositoryClass="RM\PlantillaBundle\Entity\GrupoSlotsRepository")
 */
class GrupoSlots implements GrupoSlotsInterface
{
    const CREATIVIDADES = 2;
    const PROMOCION =1;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint", nullable=true)
     * @Assert\NotBlank()
     */
    private $tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     * @Assert\NotBlank()
     */
    private $estado;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_img_producto", type="boolean", nullable=true)
     */
    private $mImgProducto;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_precio", type="boolean", nullable=true)
     */
    private $mPrecio;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_volumen", type="boolean", nullable=true)
     */
    private $mVolumen;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_condiciones", type="boolean", nullable=true)
     */
    private $mCondiciones;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_img_marca", type="boolean", nullable=true)
     */
    private $mImgMarca;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_texto", type="boolean", nullable=true)
     */
    private $mTexto;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_voucher", type="boolean", nullable=true)
     */
    private $mVoucher;

    /**
     * @var bool
     *
     * @ORM\Column(name="m_fidelizacion", type="boolean", nullable=true)
     */
    private $mFidelizacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_grupo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGrupo;

    /**
     * @var \RM\PlantillaBundle\Entity\TamanyoImagen
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\TamanyoImagen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tamanyo_img_producto", referencedColumnName="id_tamanyo")
     * })
     * @Assert\NotBlank()
     */
    private $idTamanyoImgProducto;

    /**
     * @var \RM\PlantillaBundle\Entity\Plantilla
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\Plantilla", inversedBy="gruposSlots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_plantilla", referencedColumnName="id_plantilla")
     * })
     */
    private $idPlantilla;

    /**
     * @var \RM\PlantillaBundle\Entity\TamanyoImagen
     *
     * @ORM\ManyToOne(targetEntity="RM\PlantillaBundle\Entity\TamanyoImagen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tamanyo_img_marca", referencedColumnName="id_tamanyo")
     * })
     */
    private $idTamanyoImgMarca;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_slots", type="integer", options={"default":0})
     * @Assert\NotBlank()
     */
    private $numSlots;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="RM\PlantillaBundle\Entity\Slot", mappedBy="idGrupo")
     */
    private $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
    }



    /**
     * Set nombre
     *
     * @param string $nombre
     * @return GrupoSlots
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
     * Set tipo
     *
     * @param int $tipo
     * @return GrupoSlots
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
     * Set estado
     *
     * @param int $estado
     * @return GrupoSlots
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
     * Set mImgProducto
     *
     * @param int $mImgProducto
     * @return GrupoSlots
     */
    public function setMImgProducto($mImgProducto)
    {
        $this->mImgProducto = $mImgProducto;
    
        return $this;
    }

    /**
     * Get mImgProducto
     *
     * @return int
     */
    public function getMImgProducto()
    {
        return $this->mImgProducto;
    }

    /**
     * Set mPrecio
     *
     * @param int $mPrecio
     * @return GrupoSlots
     */
    public function setMPrecio($mPrecio)
    {
        $this->mPrecio = $mPrecio;
    
        return $this;
    }

    /**
     * Get mPrecio
     *
     * @return int
     */
    public function getMPrecio()
    {
        return $this->mPrecio;
    }

    /**
     * Set mVolumen
     *
     * @param int $mVolumen
     * @return GrupoSlots
     */
    public function setMVolumen($mVolumen)
    {
        $this->mVolumen = $mVolumen;
    
        return $this;
    }

    /**
     * Get mVolumen
     *
     * @return int
     */
    public function getMVolumen()
    {
        return $this->mVolumen;
    }

    /**
     * Set mCondiciones
     *
     * @param int $mCondiciones
     * @return GrupoSlots
     */
    public function setMCondiciones($mCondiciones)
    {
        $this->mCondiciones = $mCondiciones;
    
        return $this;
    }

    /**
     * Get mCondiciones
     *
     * @return int
     */
    public function getMCondiciones()
    {
        return $this->mCondiciones;
    }

    /**
     * Set mImgMarca
     *
     * @param int $mImgMarca
     * @return GrupoSlots
     */
    public function setMImgMarca($mImgMarca)
    {
        $this->mImgMarca = $mImgMarca;
    
        return $this;
    }

    /**
     * Get mImgMarca
     *
     * @return int
     */
    public function getMImgMarca()
    {
        return $this->mImgMarca;
    }

    /**
     * Set mTexto
     *
     * @param int $mTexto
     * @return GrupoSlots
     */
    public function setMTexto($mTexto)
    {
        $this->mTexto = $mTexto;
    
        return $this;
    }

    /**
     * Get mTexto
     *
     * @return int
     */
    public function getMTexto()
    {
        return $this->mTexto;
    }

    /**
     * Set mVoucher
     *
     * @param int $mVoucher
     * @return GrupoSlots
     */
    public function setMVoucher($mVoucher)
    {
        $this->mVoucher = $mVoucher;
    
        return $this;
    }

    /**
     * Get mVoucher
     *
     * @return int
     */
    public function getMVoucher()
    {
        return $this->mVoucher;
    }

    /**
     * Set mFidelizacion
     *
     * @param int $mFidelizacion
     * @return GrupoSlots
     */
    public function setMFidelizacion($mFidelizacion)
    {
        $this->mFidelizacion = $mFidelizacion;
    
        return $this;
    }

    /**
     * Get mFidelizacion
     *
     * @return int
     */
    public function getMFidelizacion()
    {
        return $this->mFidelizacion;
    }

    /**
     * Get idGrupo
     *
     * @return int
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set idTamanyoImgProducto
     *
     * @param TamanyoImagen $idTamanyoImgProducto
     * @return GrupoSlots
     */
    public function setIdTamanyoImgProducto(TamanyoImagen $idTamanyoImgProducto = null)
    {
        $this->idTamanyoImgProducto = $idTamanyoImgProducto;
    
        return $this;
    }

    /**
     * Get idTamanyoImgProducto
     *
     * @return TamanyoImagen
     */
    public function getIdTamanyoImgProducto()
    {
        return $this->idTamanyoImgProducto;
    }

    /**
     * Set idPlantilla
     *
     * @param PlantillaInterface $idPlantilla
     * @return GrupoSlots
     */
    public function setIdPlantilla(PlantillaInterface $idPlantilla = null)
    {
        $this->idPlantilla = $idPlantilla;
    
        return $this;
    }

    /**
     * Get idPlantilla
     *
     * @return PlantillaInterface
     */
    public function getIdPlantilla()
    {
        return $this->idPlantilla;
    }

    /**
     * Set idTamanyoImgMarca
     *
     * @param TamanyoImagen $idTamanyoImgMarca
     * @return GrupoSlots
     */
    public function setIdTamanyoImgMarca(TamanyoImagen $idTamanyoImgMarca = null)
    {
        $this->idTamanyoImgMarca = $idTamanyoImgMarca;
    
        return $this;
    }

    /**
     * Get idTamanyoImgMarca
     *
     * @return TamanyoImagen
     */
    public function getIdTamanyoImgMarca()
    {
        return $this->idTamanyoImgMarca;
    }

    /**
     * Set numSlots
     *
     * @param integer $numSlots
     * @return GrupoSlots
     */
    public function setNumSlots($numSlots)
    {
        $this->numSlots = $numSlots;
    
        return $this;
    }

    /**
     * Get numSlots
     *
     * @return integer 
     */
    public function getNumSlots()
    {
        return $this->numSlots;
    }


  public function __toString(){
    return $this->idGrupo.'';
  }

    /**
     * Add slots
     *
     * @param Slot $slots
     * @return GrupoSlots
     */
    public function addSlot(Slot $slots)
    {
        $this->slots[] = $slots;
    
        return $this;
    }

    /**
     * Remove slots
     *
     * @param Slot $slots
     */
    public function removeSlot(Slot $slots)
    {
        $this->slots->removeElement($slots);
    }

    /**
     * Get slots
     *
     * @return Collection
     */
    public function getSlots()
    {
        return $this->slots;
    }
}