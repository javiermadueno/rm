<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/08/2015
 * Time: 8:43
 */

namespace RM\ProductoBundle\Form\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Manager\NumPromocionesManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;


class NumPromocionesEditFormHandler
{
    /** @var  ArrayCollection */
    private $numPromocionesOriginales;

    /**
     * @var NumPromocionesManager
     */
    private $manager;

    /**
     * @var InstanciaComunicacion
     */
    private $instancia;

    /**
     * @var GrupoSlots
     */
    private $grupo;

    /**
     * @param NumPromocionesManager $manager
     */
    public function __construct(NumPromocionesManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ArrayCollection $numPromocionesOriginales
     *
     * @return $this
     */
    public function setNumPromociones(ArrayCollection $numPromocionesOriginales)
    {
        $this->numPromocionesOriginales = $numPromocionesOriginales;
        return $this;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return $this
     */
    public function setInstancia(InstanciaComunicacion $instancia)
    {
        $this->instancia = $instancia;
        return $this;
    }

    /**
     * @param GrupoSlots $grupo
     *
     * @return $this
     */
    public function setGrupoSlot(GrupoSlots $grupo)
    {
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * @return InstanciaComunicacion
     */
    private function getInstancia()
    {
        if (!$this->instancia instanceof InstanciaComunicacion ) {
            throw new \InvalidArgumentException('No se ha especificado ninguna instancia de comununicación');
        }

        return $this->instancia;
    }

    /**
     * @return GrupoSlots
     */
    private function getGrupo()
    {
        if (!$this->grupo instanceof GrupoSlots) {
            throw new \InvalidArgumentException('No se ha esecificado el grupo de slot');
        }

        return $this->grupo;
    }


    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     * @throws DBALException
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request)
    {

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $data = $form->getData();
        $numPromocionesNuevos = $data['num_promocion'];

        $this->removeNumPromociones($this->numPromocionesOriginales, $numPromocionesNuevos);

        $this->createNewNumPromociones($this->numPromocionesOriginales, $numPromocionesNuevos);

        try {
            $this->manager->flush();
        } catch (DBALException $ex) {
            throw $ex;
        }

        return true;
    }

    /**
     * @param ArrayCollection $numPromocionesOriginales
     * @param ArrayCollection $numPromocionesNuevos
     */
    private function removeNumPromociones(
        ArrayCollection $numPromocionesOriginales,
        ArrayCollection $numPromocionesNuevos
    ) {
        foreach ($numPromocionesOriginales as $numPromocionOriginal) {
            if (false === $numPromocionesNuevos->contains($numPromocionOriginal)) {
                $this->getInstancia()->removeNumPromocion($numPromocionOriginal);
                $this->manager->remove($numPromocionOriginal);
            }
        }
    }

    /**
     * @param ArrayCollection $numPromocionesOriginales
     * @param ArrayCollection $numPromocionesNuevos
     */
    private function createNewNumPromociones(
        ArrayCollection $numPromocionesOriginales,
        ArrayCollection $numPromocionesNuevos
    ) {
        /** @var NumPromociones $numPromocion */
        foreach ($numPromocionesNuevos as $numPromocion) {
            if (false === $numPromocionesOriginales->contains($numPromocion)) {
                $numPromocion
                    ->setIdInstancia($this->getInstancia())
                    ->setIdGrupo($this->getGrupo())
                ;
                $this
                    ->getInstancia()
                    ->addNumPromocion($numPromocion);

                $this->manager->persist($numPromocion);
            }
        }

        $this->manager->flush();
    }

} 