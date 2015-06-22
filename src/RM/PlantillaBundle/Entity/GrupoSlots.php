<?php

namespace RM\PlantillaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * GrupoSlots
 *
 * @ORM\Table(name="grupo_slots")
 * @ORM\Entity(repositoryClass="RM\PlantillaBundle\Entity\GrupoSlotsRepository")
 */
class GrupoSlots implements GrupoSlotsInterface
{
    const CREATIVIDADES = 2;
    const PROMOCION = 1;

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
     * @return GrupoSlots
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return smallint
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set tipo
     *
     * @param smallint $tipo
     *
     * @return GrupoSlots
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

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
     * Set estado
     *
     * @param smallint $estado
     *
     * @return GrupoSlots
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get mImgProducto
     *
     * @return smallint
     */
    public function getMImgProducto()
    {
        return $this->mImgProducto;
    }

    /**
     * Set mImgProducto
     *
     * @param smallint $mImgProducto
     *
     * @return GrupoSlots
     */
    public function setMImgProducto($mImgProducto)
    {
        $this->mImgProducto = $mImgProducto;

        return $this;
    }

    /**
     * Get mPrecio
     *
     * @return smallint
     */
    public function getMPrecio()
    {
        return $this->mPrecio;
    }

    /**
     * Set mPrecio
     *
     * @param smallint $mPrecio
     *
     * @return GrupoSlots
     */
    public function setMPrecio($mPrecio)
    {
        $this->mPrecio = $mPrecio;

        return $this;
    }

    /**
     * Get mVolumen
     *
     * @return smallint
     */
    public function getMVolumen()
    {
        return $this->mVolumen;
    }

    /**
     * Set mVolumen
     *
     * @param smallint $mVolumen
     *
     * @return GrupoSlots
     */
    public function setMVolumen($mVolumen)
    {
        $this->mVolumen = $mVolumen;

        return $this;
    }

    /**
     * Get mCondiciones
     *
     * @return smallint
     */
    public function getMCondiciones()
    {
        return $this->mCondiciones;
    }

    /**
     * Set mCondiciones
     *
     * @param smallint $mCondiciones
     *
     * @return GrupoSlots
     */
    public function setMCondiciones($mCondiciones)
    {
        $this->mCondiciones = $mCondiciones;

        return $this;
    }

    /**
     * Get mImgMarca
     *
     * @return smallint
     */
    public function getMImgMarca()
    {
        return $this->mImgMarca;
    }

    /**
     * Set mImgMarca
     *
     * @param smallint $mImgMarca
     *
     * @return GrupoSlots
     */
    public function setMImgMarca($mImgMarca)
    {
        $this->mImgMarca = $mImgMarca;

        return $this;
    }

    /**
     * Get mTexto
     *
     * @return smallint
     */
    public function getMTexto()
    {
        return $this->mTexto;
    }

    /**
     * Set mTexto
     *
     * @param smallint $mTexto
     *
     * @return GrupoSlots
     */
    public function setMTexto($mTexto)
    {
        $this->mTexto = $mTexto;

        return $this;
    }

    /**
     * Get mVoucher
     *
     * @return smallint
     */
    public function getMVoucher()
    {
        return $this->mVoucher;
    }

    /**
     * Set mVoucher
     *
     * @param smallint $mVoucher
     *
     * @return GrupoSlots
     */
    public function setMVoucher($mVoucher)
    {
        $this->mVoucher = $mVoucher;

        return $this;
    }

    /**
     * Get mFidelizacion
     *
     * @return smallint
     */
    public function getMFidelizacion()
    {
        return $this->mFidelizacion;
    }

    /**
     * Set mFidelizacion
     *
     * @param smallint $mFidelizacion
     *
     * @return GrupoSlots
     */
    public function setMFidelizacion($mFidelizacion)
    {
        $this->mFidelizacion = $mFidelizacion;

        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return integer
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Get idTamanyoImgProducto
     *
     * @return \RM\PlantillaBundle\Entity\TamanyoImagen
     */
    public function getIdTamanyoImgProducto()
    {
        return $this->idTamanyoImgProducto;
    }

    /**
     * Set idTamanyoImgProducto
     *
     * @param \RM\PlantillaBundle\Entity\TamanyoImagen $idTamanyoImgProducto
     *
     * @return GrupoSlots
     */
    public function setIdTamanyoImgProducto(\RM\PlantillaBundle\Entity\TamanyoImagen $idTamanyoImgProducto = null)
    {
        $this->idTamanyoImgProducto = $idTamanyoImgProducto;

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
     * Set idPlantilla
     *
     * @param PlantillaInterface $idPlantilla
     *
     * @return GrupoSlots
     */
    public function setIdPlantilla(PlantillaInterface $idPlantilla = null)
    {
        $this->idPlantilla = $idPlantilla;

        return $this;
    }

    /**
     * Get idTamanyoImgMarca
     *
     * @return \RM\PlantillaBundle\Entity\TamanyoImagen
     */
    public function getIdTamanyoImgMarca()
    {
        return $this->idTamanyoImgMarca;
    }

    /**
     * Set idTamanyoImgMarca
     *
     * @param \RM\PlantillaBundle\Entity\TamanyoImagen $idTamanyoImgMarca
     *
     * @return GrupoSlots
     */
    public function setIdTamanyoImgMarca(\RM\PlantillaBundle\Entity\TamanyoImagen $idTamanyoImgMarca = null)
    {
        $this->idTamanyoImgMarca = $idTamanyoImgMarca;

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

    /**
     * Set numSlots
     *
     * @param integer $numSlots
     *
     * @return GrupoSlots
     */
    public function setNumSlots($numSlots)
    {
        $this->numSlots = $numSlots;

        return $this;
    }

    public function __toString()
    {
        return $this->idGrupo . '';
    }

    /**
     * Add slots
     *
     * @param \RM\PlantillaBundle\Entity\Slot $slots
     *
     * @return GrupoSlots
     */
    public function addSlot(\RM\PlantillaBundle\Entity\Slot $slots)
    {
        $this->slots[] = $slots;

        return $this;
    }

    /**
     * Remove slots
     *
     * @param \RM\PlantillaBundle\Entity\Slot $slots
     */
    public function removeSlot(\RM\PlantillaBundle\Entity\Slot $slots)
    {
        $this->slots->removeElement($slots);
    }

    /**
     * Get slots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlots()
    {
        return $this->slots;
    }
}