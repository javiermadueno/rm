<?php

namespace RM\SegmentoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use RM\CategoriaBundle\Entity\Categoria;

/**
 * Segmento
 *
 * @ORM\Table(name="segmento")
 * @ORM\Entity(repositoryClass="RM\SegmentoBundle\Entity\SegmentoRepository")
 */
class Segmento implements \JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var smallint
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Tipo")
     * @ORM\JoinColumn(name="id_tipo_variable", referencedColumnName="id_tipo_variable")
     */
    private $tipo;

    /**
     * @var text
     *
     * @ORM\Column(name="query", type="text", length=255, nullable=true)
     */
    private $query;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_segmento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSegmento;

    /**
     * @var \RM\DiscretasBundle\Entity\Vid
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\Vid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vid", referencedColumnName="id_vid")
     * })
     */
    private $idVid;

    /**
     * @var \RM\TransformadasBundle\Entity\Vt
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\Vt")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vt", referencedColumnName="id_vt")
     * })
     */
    private $idVt;

    /**
     * @var \RM\TransformadasBundle\Entity\Vt
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\VtSegmento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vt_segmento", referencedColumnName="id_vt_segmento")
     * })
     */
    private $idVtSegmento;

    /**
     * @var \RM\DiscretasBundle\Entity\VidSegmento
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\VidSegmento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vid_segmento", referencedColumnName="id_vid_segmento")
     * })
     */
    private $idVidSegmento;

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
     * @var \RM\TransformadasBundle\Entity\VtGrupo
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\VtGrupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_grupo", referencedColumnName="id_grupo")
     * })
     */
    private $idGrupo;

    /**
     * @var \RM\TransformadasBundle\Entity\VtIntervalo
     *
     * @ORM\ManyToOne(targetEntity="RM\TransformadasBundle\Entity\VtIntervalo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_intervalo", referencedColumnName="id_intervalo")
     * })
     */
    private $idIntervalo;

    /**
     * @var \RM\LinealesBundle\Entity\Vil
     *
     * @ORM\ManyToOne(targetEntity="RM\LinealesBundle\Entity\Vil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vil", referencedColumnName="id_vil")
     * })
     */
    private $idVil;

    /**
     * @var \RM\DiscretasBundle\Entity\VidGrupoSegmento
     *
     * @ORM\ManyToOne(targetEntity="RM\DiscretasBundle\Entity\VidGrupoSegmento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vid_grupo_segmento", referencedColumnName="id_vid_grupo_segmento")
     * })
     */
    private $idVidGrupoSegmento;

    /**
     * @var
     * @ORM\Column(name="es_nuevo", type="smallint", length=6, nullable=true)
     */
    private $esNuevo;

    /**
     * @var integer
     * @ORM\Column(name="c_clave", type="integer")
     */
    private $c_clave;

    /**
     * @var \DateTime
     * @ORM\Column(name="c_fecha_ini", type="datetime")
     */
    private $c_fecha_ini;

    /**
     * @var \DateTime
     * @ORM\Column(name="c_fecha_fin", type="datetime")
     */
    private $c_fecha_fin;

    /**
     * @var \RM\ProductoBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="RM\ProductoBundle\Entity\Proveedor")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id_proveedor")
     */
    private $idProveedor;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     *
     * @return Segmento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer
     */
    public function getNombre()
    {
        return $this->nombre;
    }


    /**
     * Set query
     *
     * @param text $query
     *
     * @return Segmento
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return text
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set estado
     *
     * @param smallint $estado
     *
     * @return Segmento
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
     * Get idSegmento
     *
     * @return integer
     */
    public function getIdSegmento()
    {
        return $this->idSegmento;
    }

    /**
     * Set idVid
     *
     * @param \RM\DiscretasBundle\Entity\Vid $idVid
     *
     * @return Segmento
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
     * Set idVt
     *
     * @param \RM\TransformadasBundle\Entity\Vt $idVt
     *
     * @return Segmento
     */
    public function setIdVt(\RM\TransformadasBundle\Entity\Vt $idVt = null)
    {
        $this->idVt = $idVt;

        return $this;
    }

    /**
     * Get idVt
     *
     * @return \RM\TransformadasBundle\Entity\Vt
     */
    public function getIdVt()
    {
        return $this->idVt;
    }

    /**
     * Set idVtSegmento
     *
     * @param \RM\TransformadasBundle\Entity\VtSegmento $idVtSegmento
     *
     * @return Segmento
     */
    public function setIdVtSegmento(\RM\TransformadasBundle\Entity\VtSegmento $idVtSegmento = null)
    {
        $this->idVtSegmento = $idVtSegmento;

        return $this;
    }

    /**
     * Get idVtSegmento
     *
     * @return \RM\TransformadasBundle\Entity\VtSegmento
     */
    public function getIdVtSegmento()
    {
        return $this->idVtSegmento;
    }

    /**
     * Set idVidSegmento
     *
     * @param \RM\DiscretasBundle\Entity\VidSegmento $idVidSegmento
     *
     * @return Segmento
     */
    public function setIdVidSegmento(\RM\DiscretasBundle\Entity\VidSegmento $idVidSegmento = null)
    {
        $this->idVidSegmento = $idVidSegmento;

        return $this;
    }

    /**
     * Get idVidSegmento
     *
     * @return \RM\DiscretasBundle\Entity\VidSegmento
     */
    public function getIdVidSegmento()
    {
        return $this->idVidSegmento;
    }

    /**
     * Set idMarca
     *
     * @param \RM\ProductoBundle\Entity\Marca $idMarca
     *
     * @return Segmento
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
     *
     * @return Segmento
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
     * Set idGrupo
     *
     * @param \RM\TransformadasBundle\Entity\VtGrupo $idGrupo
     *
     * @return Segmento
     */
    public function setIdGrupo(\RM\TransformadasBundle\Entity\VtGrupo $idGrupo = null)
    {
        $this->idGrupo = $idGrupo;

        return $this;
    }

    /**
     * Get idGrupo
     *
     * @return \RM\TransformadasBundle\Entity\VtGrupo
     */
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }

    /**
     * Set idIntervalo
     *
     * @param \RM\TransformadasBundle\Entity\VtIntervalo $idIntervalo
     *
     * @return Segmento
     */
    public function setIdIntervalo(\RM\TransformadasBundle\Entity\VtIntervalo $idIntervalo = null)
    {
        $this->idIntervalo = $idIntervalo;

        return $this;
    }

    /**
     * Get idIntervalo
     *
     * @return \RM\TransformadasBundle\Entity\VtIntervalo
     */
    public function getIdIntervalo()
    {
        return $this->idIntervalo;
    }

    /**
     * Set idVil
     *
     * @param \RM\LinealesBundle\Entity\Vil $idVil
     *
     * @return Segmento
     */
    public function setIdVil(\RM\LinealesBundle\Entity\Vil $idVil = null)
    {
        $this->idVil = $idVil;

        return $this;
    }

    /**
     * Get idVil
     *
     * @return \RM\LinealesBundle\Entity\Vil
     */
    public function getIdVil()
    {
        return $this->idVil;
    }

    /**
     * Set idVidGrupoSegmento
     *
     * @param \RM\DiscretasBundle\Entity\VidGrupoSegmento $idVidGrupoSegmento
     *
     * @return Segmento
     */
    public function setIdVidGrupoSegmento(\RM\DiscretasBundle\Entity\VidGrupoSegmento $idVidGrupoSegmento = null)
    {
        $this->idVidGrupoSegmento = $idVidGrupoSegmento;

        return $this;
    }

    /**
     * Get idVidGrupoSegmento
     *
     * @return \RM\DiscretasBundle\Entity\VidGrupoSegmento
     */
    public function getIdVidGrupoSegmento()
    {
        return $this->idVidGrupoSegmento;
    }


    /**
     * Set tipo
     *
     * @param \RM\DiscretasBundle\Entity\Tipo $tipo
     *
     * @return Segmento
     */
    public function setTipo(\RM\DiscretasBundle\Entity\Tipo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \RM\DiscretasBundle\Entity\Tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set esNuevo
     *
     * @param integer $esNuevo
     *
     * @return Segmento
     */
    public function setEsNuevo($esNuevo)
    {
        $this->esNuevo = $esNuevo;

        return $this;
    }

    /**
     * Get esNuevo
     *
     * @return integer
     */
    public function getEsNuevo()
    {
        return $this->esNuevo;
    }

    /**
     * Set c_clave
     *
     * @param integer $cClave
     *
     * @return Segmento
     */
    public function setCClave($cClave)
    {
        $this->c_clave = $cClave;

        return $this;
    }

    /**
     * Get c_clave
     *
     * @return integer
     */
    public function getCClave()
    {
        return $this->c_clave;
    }

    /**
     * Set c_fecha_ini
     *
     * @param \DateTime $cFechaIni
     *
     * @return Segmento
     */
    public function setCFechaIni($cFechaIni)
    {
        $this->c_fecha_ini = $cFechaIni;

        return $this;
    }

    /**
     * Get c_fecha_ini
     *
     * @return \DateTime
     */
    public function getCFechaIni()
    {
        return $this->c_fecha_ini;
    }

    /**
     * Set c_fecha_fin
     *
     * @param \DateTime $cFechaFin
     *
     * @return Segmento
     */
    public function setCFechaFin($cFechaFin)
    {
        $this->c_fecha_fin = $cFechaFin;

        return $this;
    }

    /**
     * Get c_fecha_fin
     *
     * @return \DateTime
     */
    public function getCFechaFin()
    {
        return $this->c_fecha_fin;
    }

    /**
     * Set idProveedor
     *
     * @param \RM\ProductoBundle\Entity\Proveedor $idProveedor
     *
     * @return Segmento
     */
    public function setIdProveedor(\RM\ProductoBundle\Entity\Proveedor $idProveedor = null)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return \RM\ProductoBundle\Entity\Proveedor
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }

    public function jsonSerialize()
    {
        return [
            'id'        => $this->idSegmento,
            'nombre'    => $this->nombre,
            'categoria' => is_null($this->idCategoria) ? '' : $this->idCategoria->getNombre(),
            'proveedor' => is_null($this->idProveedor) ? '' : $this->idProveedor->getNombre(),
            'marca'     => is_null($this->idMarca) ? '' : $this->idMarca->getNombre(),
            'cClave'    => $this->c_clave,
            'cFechaIni' => is_null($this->c_fecha_ini) ? '' : $this->c_fecha_ini->format('Y-m-d'),
            'cFechaFin' => is_null($this->c_fecha_fin) ? '' : $this->c_fecha_fin->format('Y-m-d')

        ];
    }
}