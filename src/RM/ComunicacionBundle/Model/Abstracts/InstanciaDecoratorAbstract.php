<?php
/**
 * Created by PhpStorm.
 * User: javi
 * Date: 29/08/15
 * Time: 19:43
 */

namespace RM\ComunicacionBundle\Model\Abstracts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use RM\CategoriaBundle\Entity\Categoria;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;


abstract class InstanciaDecoratorAbstract extends InstanciaComunicacionAbstract
{
    /** @var  InstanciaComunicacion */
    protected $instancia;

    /** @var  ArrayCollection */
    protected $gruposSlots;

    /** @var  ArrayCollection */
    protected $numPromociones;

    /** @var  ArrayCollection */
    protected $segmentadas;

    /** @var  ArrayCollection */
    protected $genericas;

    /** @var  ArrayCollection */
    protected $categoriasPermitidas;

    /**
     * @param InstanciaComunicacionAbstract $instancia
     * @param ArrayCollection               $categoriasPermitidas
     */
    public function __construct(InstanciaComunicacionAbstract $instancia, ArrayCollection $categoriasPermitidas)
    {
        $this->instancia            = $instancia;
        $this->categoriasPermitidas = $categoriasPermitidas;
        $this->segmentadas          = new ArrayCollection();
        $this->genericas            = new ArrayCollection();
        $this->getNumPromociones();
        $this->getGruposSlots();
    }

    /**
     * @return Arraycollection
     */
    public function getNumPromociones()
    {
        if (!$this->numPromociones) {
            $this->numPromociones = $this->instancia
                ->getNumPromociones()
                ->filter(
                    function (NumPromociones $numPromociones) {
                        return
                            (
                                $this->categoriasPermitidas->isEmpty()
                                || $this->categoriasPermitidas->contains($numPromociones->getIdCategoria())
                            )
                            && $this->getTipoNumPromocion() === $numPromociones->getIdGrupo()->getTipo();
                    })
            ;
        }

        $sort = Criteria::create();
        $sort->orderBy(['nombre' => Criteria::ASC]);

        return $this->numPromociones->matching($sort);
    }

    protected abstract function getTipoNumPromocion();

    /**
     * @return ArrayCollection
     */
    public function getGruposSlots()
    {
        if (!$this->gruposSlots) {
            $this->gruposSlots =
                $this->instancia
                    ->getGruposSlots()
                    ->filter(
                        function (GrupoSlots $grupo) {
                            return $this->getTipoNumPromocion() === $grupo->getTipo();
                        }
                    )
            ;
        }

        return $this->gruposSlots;
    }

    /**
     * @return int
     */
    public function getIdInstancia()
    {
        return $this
            ->instancia
            ->getIdInstancia()
            ;
    }

    /**
     * @return SegmentoComunicacion
     */
    public function getIdSegmentoComunicacion()
    {
        return $this
            ->instancia
            ->getIdSegmentoComunicacion()
            ;
    }

    /**
     * @return Fases
     */
    public function getFase()
    {
        return $this
            ->instancia
            ->getFase()
            ;
    }

    /**
     * @return int
     */
    public function getEstado()
    {
        return $this
            ->instancia
            ->getEstado()
            ;
    }

    /**
     * @return \Datetime
     */
    public function getFecCreacion()
    {
        return $this
            ->instancia
            ->getFecCreacion()
            ;
    }

    /**
     * @return \Datetime
     */
    public function getFecEjecucion()
    {
        return $this
            ->instancia
            ->getFecEjecucion()
            ;
    }

    /**
     * @param $categoria
     *
     * @return NumPromociones[]
     */
    public function getNumPromocionesByCategoria($categoria)
    {
        $numPromociones = $this
            ->getNumPromociones()
            ->filter(function (NumPromociones $numPromocion) use ($categoria) {
                return $categoria === $numPromocion->getIdCategoria()->getIdCategoria();
            })
        ;

        return $numPromociones->toArray();
    }

    /**
     * @param   GrupoSlots $grupo
     * @param   null       $categoria
     *
     * @return  Promocion[]
     */
    public function getPromocionesSegmentadasByGrupoSlotYCategoria(GrupoSlots $grupo, $categoria = null)
    {
        $numPromociones = $this
            ->getNumPromocionesByGrupoSlotYCategoria($grupo, $categoria)
        ;

        $segmentadas = array_reduce($numPromociones, function ($res, NumPromociones $numPromocion) {
            $res = array_merge($res, $numPromocion->getPromocionesSegmentadas()->toArray());

            return $res;
        }, []);

        return $segmentadas->toArray();
    }

    /**
     * @param   GrupoSlots $grupo
     * @param   null       $categoria
     *
     * @return  NumPromociones[]
     */
    public function getNumPromocionesByGrupoSlotYCategoria(GrupoSlots $grupo, $categoria = null)
    {
        if (!$categoria) {
            return $this
                ->getNumPromocionesByGrupo($grupo)
                ;
        }

        $numPromociones = $this
            ->getNumPromociones()
            ->filter(
                function (NumPromociones $numPromocio) use ($grupo, $categoria) {
                    return
                        $numPromocio->getIdGrupo() === $grupo &&
                        $numPromocio->getIdCategoria()->getIdCategoria() == $categoria;
                })
        ;

        if ($this->getTipoNumPromocion() === GrupoSlots::CREATIVIDADES) {
            return $numPromociones->toArray();
        }

        return $this->ordenaNumPromocionesByCategoria($numPromociones);

    }

    /**
     * @param GrupoSlots $grupo
     *
     * @return NumPromociones[]
     */
    public function getNumPromocionesByGrupo(GrupoSlots $grupo)
    {
        $numPromociones = $this
            ->getNumPromociones()
            ->filter(function (NumPromociones $numPromociones) use ($grupo) {
                return $numPromociones->getIdGrupo() === $grupo;
            })
        ;

        if ($this->getTipoNumPromocion() === GrupoSlots::CREATIVIDADES) {
            return $numPromociones->toArray();
        }

        return $this->ordenaNumPromocionesByCategoria($numPromociones);
    }

    /**
     * @param ArrayCollection $numPromociones
     *
     * @return NumPromociones[]
     */
    protected function ordenaNumPromocionesByCategoria(ArrayCollection $numPromociones)
    {
        $iterator = $numPromociones->getIterator();
        $iterator->uasort(function (NumPromociones $a, NumPromociones $b) {
            return strcmp($a->getIdCategoria()->getNombre(), $b->getIdCategoria()->getNombre());
        })
        ;

        return iterator_to_array($iterator);
    }

    /**
     * @param   GrupoSlots $grupo
     * @param   null       $categoria
     *
     * @return  Promocion[]
     */
    public function getPromocionesGenericasByGrupoSlotYCategoria(GrupoSlots $grupo, $categoria = null)
    {

        $numPromociones = $this->getNumPromocionesByGrupoSlotYCategoria($grupo, $categoria);

        $genericas = array_reduce($numPromociones, function ($res, NumPromociones $numPromocion) {
            $res = array_merge($res, $numPromocion->getPromocionesGenericas()->toArray());

            return $res;
        }, []);

        return $genericas->toArray();
    }

    /**
     * @return Categoria[]
     */
    public function getCategorias()
    {
        $numPromociones = $this->getNumPromociones()->toArray();

        $categorias = array_reduce($numPromociones, function ($res, NumPromociones $numPromocion) {
            $res[] = $numPromocion->getIdCategoria();

            return $res;
        }, []);

        return array_unique($categorias, SORT_REGULAR);
    }

    public function getPromocionesSegmentadas()
    {
        return $this->instancia->getPromocionesSegmentadas();
    }

    /**
     * @return ArrayCollection
     */
    public function getPromocionesGenericas()
    {
        return $this->instancia->getPromocionesGenericas();
    }

    /**
     * @return number
     */
    public function getTotalPromocionesAceptadas()
    {
        return $this->instancia->getTotalPromocionesAceptadas();
    }

    /**
     * @return number
     */
    public function getTotalPromocionesRechazadas()
    {
        return $this->instancia->getTotalPromocionesRechazadas();
    }

    /**
     * @return number
     */
    public function getTotalPromocionesPendientes()
    {
        return $this->instancia->getTotalPromocionesPendientes();
    }


} 