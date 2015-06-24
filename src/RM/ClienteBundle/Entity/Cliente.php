<?php

namespace RM\ClienteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="RM\ClienteBundle\Entity\ClienteRepository")
 */
class Cliente
{
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_id", type="integer", nullable=true)
     */
    private $numeroId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion1", type="string", length=255, nullable=true)
     */
    private $direccion1;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion2", type="string", length=255, nullable=true)
     */
    private $direccion2;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=255, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=255, nullable=true)
     */
    private $ciudad;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_union", type="datetime", nullable=true)
     */
    private $fechaUnion;

    /**
     * @var string
     *
     * @ORM\Column(name="tile", type="string", length=255, nullable=true)
     */
    private $tile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha__nacimiento", type="date", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_miembro_prog", type="integer", nullable=true)
     */
    private $idMiembroProg;

    /**
     * @var string
     *
     * @ORM\Column(name="robbinson", type="string", length=255, nullable=true)
     */
    private $robbinson;

    /**
     * @var string
     *
     * @ORM\Column(name="opt_in", type="string", length=255, nullable=true)
     */
    private $optIn;

    /**
     * @var smallint
     *
     * @ORM\Column(name="genero", type="smallint", nullable=true)
     */
    private $genero;

    /**
     * @var smallint
     *
     * @ORM\Column(name="estado_marital", type="smallint", nullable=true)
     */
    private $estadoMarital;

    /**
     * @var smallint
     *
     * @ORM\Column(name="num_hijos", type="smallint", nullable=true)
     */
    private $numHijos;

    /**
     * @var smallint
     *
     * @ORM\Column(name="tipo_vivienda", type="smallint", nullable=true)
     */
    private $tipoVivienda;

    /**
     * @var string
     *
     * @ORM\Column(name="personalizado1", type="string", length=255, nullable=true)
     */
    private $personalizado1;

    /**
     * @var string
     *
     * @ORM\Column(name="personalizado2", type="string", length=255, nullable=true)
     */
    private $personalizado2;

    /**
     * @var string
     *
     * @ORM\Column(name="personalizado3", type="string", length=255, nullable=true)
     */
    private $personalizado3;

    /**
     * @var string
     *
     * @ORM\Column(name="personalizado4", type="string", length=255, nullable=true)
     */
    private $personalizado4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cliente", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCliente;


    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Cliente
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
     * Set apellidos
     *
     * @param string $apellidos
     *
     * @return Cliente
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set numeroId
     *
     * @param integer $numeroId
     *
     * @return Cliente
     */
    public function setNumeroId($numeroId)
    {
        $this->numeroId = $numeroId;

        return $this;
    }

    /**
     * Get numeroId
     *
     * @return integer
     */
    public function getNumeroId()
    {
        return $this->numeroId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Cliente
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Cliente
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set direccion1
     *
     * @param string $direccion1
     *
     * @return Cliente
     */
    public function setDireccion1($direccion1)
    {
        $this->direccion1 = $direccion1;

        return $this;
    }

    /**
     * Get direccion1
     *
     * @return string
     */
    public function getDireccion1()
    {
        return $this->direccion1;
    }

    /**
     * Set direccion2
     *
     * @param string $direccion2
     *
     * @return Cliente
     */
    public function setDireccion2($direccion2)
    {
        $this->direccion2 = $direccion2;

        return $this;
    }

    /**
     * Get direccion2
     *
     * @return string
     */
    public function getDireccion2()
    {
        return $this->direccion2;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Cliente
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set pais
     *
     * @param string $pais
     *
     * @return Cliente
     */
    public function setPais($pais)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return string
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return Cliente
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     *
     * @return Cliente
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set fechaUnion
     *
     * @param \DateTime $fechaUnion
     *
     * @return Cliente
     */
    public function setFechaUnion($fechaUnion)
    {
        $this->fechaUnion = $fechaUnion;

        return $this;
    }

    /**
     * Get fechaUnion
     *
     * @return \DateTime
     */
    public function getFechaUnion()
    {
        return $this->fechaUnion;
    }

    /**
     * Set tile
     *
     * @param string $tile
     *
     * @return Cliente
     */
    public function setTile($tile)
    {
        $this->tile = $tile;

        return $this;
    }

    /**
     * Get tile
     *
     * @return string
     */
    public function getTile()
    {
        return $this->tile;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     *
     * @return Cliente
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set idMiembroProg
     *
     * @param integer $idMiembroProg
     *
     * @return Cliente
     */
    public function setIdMiembroProg($idMiembroProg)
    {
        $this->idMiembroProg = $idMiembroProg;

        return $this;
    }

    /**
     * Get idMiembroProg
     *
     * @return integer
     */
    public function getIdMiembroProg()
    {
        return $this->idMiembroProg;
    }

    /**
     * Set robbinson
     *
     * @param string $robbinson
     *
     * @return Cliente
     */
    public function setRobbinson($robbinson)
    {
        $this->robbinson = $robbinson;

        return $this;
    }

    /**
     * Get robbinson
     *
     * @return string
     */
    public function getRobbinson()
    {
        return $this->robbinson;
    }

    /**
     * Set optIn
     *
     * @param string $optIn
     *
     * @return Cliente
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;

        return $this;
    }

    /**
     * Get optIn
     *
     * @return string
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * Set genero
     *
     * @param smallint $genero
     *
     * @return Cliente
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return smallint
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set estadoMarital
     *
     * @param smallint $estadoMarital
     *
     * @return Cliente
     */
    public function setEstadoMarital($estadoMarital)
    {
        $this->estadoMarital = $estadoMarital;

        return $this;
    }

    /**
     * Get estadoMarital
     *
     * @return smallint
     */
    public function getEstadoMarital()
    {
        return $this->estadoMarital;
    }

    /**
     * Set numHijos
     *
     * @param smallint $numHijos
     *
     * @return Cliente
     */
    public function setNumHijos($numHijos)
    {
        $this->numHijos = $numHijos;

        return $this;
    }

    /**
     * Get numHijos
     *
     * @return smallint
     */
    public function getNumHijos()
    {
        return $this->numHijos;
    }

    /**
     * Set tipoVivienda
     *
     * @param smallint $tipoVivienda
     *
     * @return Cliente
     */
    public function setTipoVivienda($tipoVivienda)
    {
        $this->tipoVivienda = $tipoVivienda;

        return $this;
    }

    /**
     * Get tipoVivienda
     *
     * @return smallint
     */
    public function getTipoVivienda()
    {
        return $this->tipoVivienda;
    }

    /**
     * Set personalizado1
     *
     * @param string $personalizado1
     *
     * @return Cliente
     */
    public function setPersonalizado1($personalizado1)
    {
        $this->personalizado1 = $personalizado1;

        return $this;
    }

    /**
     * Get personalizado1
     *
     * @return string
     */
    public function getPersonalizado1()
    {
        return $this->personalizado1;
    }

    /**
     * Set personalizado2
     *
     * @param string $personalizado2
     *
     * @return Cliente
     */
    public function setPersonalizado2($personalizado2)
    {
        $this->personalizado2 = $personalizado2;

        return $this;
    }

    /**
     * Get personalizado2
     *
     * @return string
     */
    public function getPersonalizado2()
    {
        return $this->personalizado2;
    }

    /**
     * Set personalizado3
     *
     * @param string $personalizado3
     *
     * @return Cliente
     */
    public function setPersonalizado3($personalizado3)
    {
        $this->personalizado3 = $personalizado3;

        return $this;
    }

    /**
     * Get personalizado3
     *
     * @return string
     */
    public function getPersonalizado3()
    {
        return $this->personalizado3;
    }

    /**
     * Set personalizado4
     *
     * @param string $personalizado4
     *
     * @return Cliente
     */
    public function setPersonalizado4($personalizado4)
    {
        $this->personalizado4 = $personalizado4;

        return $this;
    }

    /**
     * Get personalizado4
     *
     * @return string
     */
    public function getPersonalizado4()
    {
        return $this->personalizado4;
    }

    /**
     * Get idCliente
     *
     * @return integer
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }
}