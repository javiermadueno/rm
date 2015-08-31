<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RM\CategoriaBundle\Entity\Categoria;
use RM\ProductoBundle\Entity\Proveedor;

/**
 * VidGrupoSegmento
 *
 * @ORM\Table(name="vid_grupo_segmento")
 * @ORM\Entity(repositoryClass="RM\DiscretasBundle\Entity\VidGrupoSegmentoRepository")
 */
class VidGrupoSegmento implements VidCriterioInterface
{
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_vid_grupo_segmento", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idVidGrupoSegmento;
	
	/**
	 * @var \RM\DiscretasBundle\Entity\Vid
	 *
	 * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Vid" ,cascade={"persist"} , fetch="EAGER")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_vid", referencedColumnName="id_vid")
	 * })
	 */
	private $idVid;
	
	/**
	 * @var \RM\ProductoBundle\Entity\Marca
	 *
	 * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Marca")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_marca", referencedColumnName="id_marca")
	 * })
	 */
	private $idMarca;
	
	/**
	 * @var Categoria
	 *
	 * @ORM\ManyToOne(targetEntity="RM\CategoriaBundle\Entity\Categoria")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id_categoria")
	 * })
	 */
	private $idCategoria;
	
	/**
	 * @var Proveedor
	 *
	 * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Proveedor")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id_proveedor")
	 * })
	 */
	private $idProveedor;
	
	
	/**
     * @var integer
     *
     * @ORM\Column(name="meses_n", type="integer", nullable=true)
     */
    private $mesesN;

    /**
     * @var integer
     *
     * @ORM\Column(name="meses_m", type="integer", nullable=true)
     */
    private $mesesM;


    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="personalizado" , type="integer", nullable=true)
     */
    private $personalizado;
    

	/**
	 * Set estado
	 *
	 * @param int $estado
	 * @return VidGrupoSegmento
	 */
	public function setEstado($estado)
	{
		$this->estado = $estado;

		return $this;
	}

	/**
	 * Get estado
	 *
	 * @return integer
	 */
	public function getEstado()
	{
		return $this->estado;
	}

	/**
	 * Get VidGrupoSegmento
	 *
	 * @return integer
	 */
	public function getIdVidGrupoSegmento()
	{
		return $this->idVidGrupoSegmento;
	}

	/**
	 * Set idVid
	 *
	 * @param \RM\DiscretasBundle\Entity\Vid $idVid
	 * @return VidGrupoSegmento
	 */
	public function setIdVid(\RM\DiscretasBundle\Entity\Vid $idVid = null)
	{
		$this->idVid = $idVid;

		return $this;
	}

	/**
	 * Get idVid
	 *
	 * @return \RM\DiscretasBundle\Entity\Vid
	 */
	public function getIdVid()
	{
		return $this->idVid;
	}

	/**
	 * Set idMarca
	 *
	 * @param \RM\ProductoBundle\Entity\Marca $idMarca
	 * @return VidGrupoSegmento
	 */
	public function setIdMarca(\RM\ProductoBundle\Entity\Marca $idMarca = null)
	{
		$this->idMarca = $idMarca;

		return $this;
	}

	/**
	 * Get idMarca
	 *
	 * @return \RM\ProductoBundle\Entity\Marca
	 */
	public function getIdMarca()
	{
		return $this->idMarca;
	}

	/**
	 * Set idCategoria
	 *
	 * @param \RM\CategoriaBundle\Entity\Categoria $idCategoria
	 * @return VidGrupoSegmento
	 */
	public function setIdCategoria(\RM\CategoriaBundle\Entity\Categoria $idCategoria = null)
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
	 * Set idProveedor
	 *
	 * @param Proveedor $idProveedor
	 * @return VidGrupoSegmento
	 */
	public function setIdProveedor(Proveedor $idProveedor = null)
	{
		$this->idProveedor = $idProveedor;
	
		return $this;
	}
	
	/**
	 * Get idProveedor
	 *
	 * @return Proveedor
	 */
	public function getIdProveedor()
	{
		return $this->idProveedor;
	}
	
	/**
	 * Set mesesN
	 *
	 * @param integer $mesesN
	 * @return VidGrupoSegmento
	 */
	public function setMesesN($mesesN)
	{
		$this->mesesN = $mesesN;
	
		return $this;
	}
	
	/**
	 * Get mesesN
	 *
	 * @return integer
	 */
	public function getMesesN()
	{
		return $this->mesesN;
	}
	
	/**
	 * Set mesesM
	 *
	 * @param integer $mesesM
	 * @return VidGrupoSegmento
	 */
	public function setMesesM($mesesM)
	{
		$this->mesesM = $mesesM;
	
		return $this;
	}
	
	/**
	 * Get mesesM
	 *
	 * @return integer
	 */
	public function getMesesM()
	{
		return $this->mesesM;
	}

	
	/**
	 * @return integer
	 */
	public function getPersonalizado()
	{
		return $this->personalizado;
	}
	
	/**
	 *
	 * @param integer $personalizado
	 * @return VidGrupoSegmento
	 */
	public function setPersonalizado($personalizado)
	{
		$this->personalizado =  $personalizado;
		
		return $this;
	}
	
	
}