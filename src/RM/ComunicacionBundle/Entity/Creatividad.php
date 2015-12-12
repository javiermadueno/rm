<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Creatividad
 *
 * @ORM\Table(name="creatividad")
 * @ORM\Entity(repositoryClass="RM\ComunicacionBundle\Entity\CreatividadRepository")
 */
class Creatividad
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id_creatividad", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idCreatividad;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
	 */
	private $nombre;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
	 */
	private $descripcion;
	
	/**
	 * @var int
	 *
	 * @ORM\Column(name="estado", type="smallint", nullable=false)
	 */
	private $estado;

    /**
     * @var string
     * @ORM\Column(name="imagen", type="string")
     */
    private $imagen;

    /**
     * @var UploadedFile
     */
    private $file;


    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    public function removeImagen($ruta)
    {
        if(!$this->imagen) {
            return;
        }

        $ruta = $ruta . $this->imagen;
        if(file_exists( $ruta)) {
            unlink($ruta);
        }

    }

    public function uploadImagen($cliente_path = '')
    {
        if(!$this->getFile() instanceof UploadedFile){
            return;
        }

        if (empty($cliente_path)) {
            throw new \Exception("No se ha definido el cliente");
        }

        $this->removeImagen($cliente_path);

        $nombre_imagen  =  $this->idCreatividad . '.' . $this->getFile()->guessExtension();
        $this->getFile()->move(
            $cliente_path,
            $nombre_imagen
        );

        // set the path property to the filename where you've saved the file

        $this->setImagen($nombre_imagen);

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


	/**
	 * Get idCreatividad
	 *
	 * @return integer
	 */

	public function getIdCreatividad()
	{
		return $this->idCreatividad;
	}

	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 * @return Creatividad
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
	 * Set descripcion
	 *
	 * @param string $descripcion
	 * @return Creatividad
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
	 * Set estado
	 *
	 * @param int $estado
	 * @return Creatividad
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
     * Set imagen
     *
     * @param string $imagen
     * @return Creatividad
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    
        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }
}