<?php

namespace RM\ComunicacionBundle\DependencyInjection;

use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\HttpFoundation\Request;

class CreatividadServicio
{

    private $em;


    public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
    }

    public function getCreatividadByFiltro($nombre)
    {
        if ($nombre === null) {
            $nombre = '';
        }

        $repo      = $this->em->getRepository('RMComunicacionBundle:Creatividad');
        $registros = $repo->obtenerCreatividadByFiltro($nombre);

        return $registros;
    }

    public function getCreatividadById($idCreatividad)
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:Creatividad');
        $registros = $repo->obtenerCreatividadById($idCreatividad);

        return $registros;
    }

    public function getCreatividadByFiltroDQL($nombre)
    {
        $repo     = $this->em->getRepository('RMComunicacionBundle:Creatividad');
        $consulta = $repo->obtenerCreatividadByFiltroDQL($nombre);

        return $consulta;
    }

    public function crearCreatividad($nombre, $descripcion)
    {
        $nuevaCreatividad = new Creatividad();
        $nuevaCreatividad->setNombre($nombre);
        $nuevaCreatividad->setDescripcion($descripcion);
        $nuevaCreatividad->setEstado(1);

        $this->em->persist($nuevaCreatividad);
        $this->em->flush();

        return $nuevaCreatividad;
    }


    //Parte de la gestión del módulo de INSIGHT


    public function getPromocionesCreatividad($idInstancia)
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:Creatividad');
        $registros = $repo->obtenerPromocionesCreatividad($idInstancia);

        return $registros;
    }

    public function getGrupoSlotsNumPromocionesCreatividad($idInstancia)
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:Creatividad');
        $registros = $repo->obtenerGrupoSlotsNumPromocionesCreatividad($idInstancia);

        return $registros;
    }

    /**
     * @param array $creatividades
     *
     * @return bool
     */
    public function guardarPromocionesCreatividad(array $creatividades)
    {
        if (empty($creatividades)) {
            return false;
        }

        foreach ($creatividades as $idNumPro => $promociones) {
            $numPromocion = $this->em->find('RMProductoBundle:NumPromociones', $idNumPro);
            if (!$numPromocion instanceof NumPromociones) {
                return false;
            }

            $segmentadas = isset($promociones['segmentada']) ? $promociones['segmentada'] : [];

            foreach ($segmentadas as $id => $datos) {
                $creatividad = $this->getCreatividad($datos['idCreatividad']);

                if ($id > 0) {
                    $promocion = $this->em->find('RMProductoBundle:Promocion', $id);

                    if (!$promocion instanceof Promocion) {
                        continue;
                    }

                    $promocion
                        ->setDescripcion($datos['descripcion'])
                        ->setNombreFiltro($datos['nombreFiltro'])
                        ->setFiltro($datos['filtro'])
                    ;

                    if ($creatividad instanceof Creatividad) {
                        $promocion->setCreatividad($creatividad);
                    }

                } else {

                    if (!$creatividad instanceof Creatividad) {
                        continue;
                    }

                    $promocion = new Promocion();
                    $promocion
                        ->setDescripcion($datos['descripcion'])
                        ->setNumPromocion($numPromocion)
                        ->setCreatividad($creatividad)
                        ->setFiltro($datos['filtro'])
                        ->setNombreFiltro($datos['nombreFiltro'])
                        ->setTipo(Promocion::TIPO_SEGMENTADA)
                        ->setAceptada(Promocion::ACEPTADA)
                        ->setEstado(1)
                    ;

                }

                $this->em->persist($promocion);
            }

            $genericas = isset($promociones['generica']) ? $promociones['generica'] : [];

            foreach ($genericas as $id => $datos) {
                $creatividad = $this->getCreatividad($datos['idCreatividad']);

                if ($id > 0) {
                    $promocion = $this->em->find('RMProductoBundle:Promocion', $id);
                    if (!$promocion instanceof Promocion) {
                        continue;
                    }

                    $promocion
                        ->setDescripcion($datos['descripcion'])
                    ;

                    if ($creatividad instanceof Creatividad) {
                        $promocion->setCreatividad($creatividad);
                    }
                } else {

                    if (!$creatividad instanceof Creatividad) {
                        continue;
                    }

                    $promocion = new Promocion();
                    $promocion
                        ->setDescripcion($datos['descripcion'])
                        ->setNumPromocion($numPromocion)
                        ->setCreatividad($creatividad)
                        ->setEstado(1)
                        ->setTipo(Promocion::TIPO_GENERICA)
                        ->setAceptada(Promocion::ACEPTADA)
                        ->setFiltro($datos['filtro'])
                    ;

                }

                $this->em->persist($promocion);
            }
        }

        try {
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $idCreatividad
     *
     * @return null|Creatividad
     */
    public function getCreatividad($idCreatividad)
    {
        if (!is_numeric($idCreatividad)) {
            return null;
        }

        $creatividad = $this->em
            ->find('RMComunicacionBundle:Creatividad', $idCreatividad)
        ;

        if (!$creatividad instanceof Creatividad) {
            return null;
        }

        return $creatividad;
    }

    public function guardarFichaCreatividadPromocion($id_instancia, $arrayInfoGSPromo, Request $request)
    {
        try {
            $respuesta = 1;
            foreach ($arrayInfoGSPromo as $grupoSlot) {
                $idGrupoSlots = $grupoSlot['idGrupo'];

                //Creatividades segmentadas

                $numSegmentadas = $grupoSlot['numSegmentadas'];
                if ($numSegmentadas > 0) {
                    $numSegmentadasSave = $grupoSlot['promCreatividadSegmentadas'];

                    //Ya creadas
                    if ($numSegmentadasSave > 0) {
                        $arrayIds = explode(",", $grupoSlot['idsPromoCrSegmentadas']);
                        foreach ($arrayIds as $idPC) {
                            $nomVarDesc = sprintf("seg_desc_%s_%s", $idGrupoSlots, $idPC);
                            $nomVarCre  = sprintf("seg_idcre_%s_%s", $idGrupoSlots, $idPC);
                            $nomVarSeg  = sprintf("seg_idseg_%s_%s", $idGrupoSlots, $idPC);

                            $varGenDesc = $request->get($nomVarDesc);
                            $varGenCre  = $request->get($nomVarCre);
                            $varGenSeg  = $request->get($nomVarSeg);

                            $objPromCre = $this->em->find('RMProductoBundle:Promocion', $idPC);

                            $objPromCre->setDescripcion($varGenDesc);
                            if (strlen($varGenCre) > 0) {
                                $objCre = $this->em->getRepository('RMComunicacionBundle:Creatividad')->find($varGenCre);
                                $objPromCre->setCreatividad($objCre);
                            }

                            $objPromCre->setFiltro($varGenSeg);
                            $objPromCre->setNombreFiltro($varGenSeg);

                            $this->em->merge($objPromCre);
                        }
                    }

                    //Nuevas
                    $numSegmentadasNuevas = $numSegmentadas - $numSegmentadasSave;
                    if ($numSegmentadasNuevas > 0) {

                        //Se obtiene el objeto de Num_promociones asignado al grupo y a la instancia
                        $objNumPro = $this->em->getRepository('RMProductoBundle:NumPromociones')->obtenerNumPromocionesCreatividadByFiltros($idGrupoSlots,
                            $id_instancia)
                        ;

                        if (sizeof($objNumPro) > 0) {
                            for ($i = 1; $i <= $numSegmentadasNuevas; $i++) {
                                $nomVarDesc = sprintf("new_seg_desc_%s_%s", $idGrupoSlots, $i);
                                $nomVarCre  = sprintf("new_seg_idcre_%s_%s", $idGrupoSlots, $i);
                                $nomVarSeg  = sprintf("new_seg_idseg_%s_%s", $idGrupoSlots, $i);

                                //Si no esta relleno la descripción, no se guardará
                                $varGenDesc = $request->get($nomVarDesc);
                                if (strlen($varGenDesc) > 0) {
                                    $varGenCre = $request->get($nomVarCre);
                                    $varGenSeg = $request->get($nomVarSeg);

                                    $promocion = new Promocion();

                                    $promocion->setDescripcion($varGenDesc);

                                    if (strlen($varGenCre) > 0) {
                                        $objCre = $this->em->getRepository('RMComunicacionBundle:Creatividad')->find($varGenCre);
                                        $promocion->setCreatividad($objCre);
                                    }

                                    $promocion->setFiltro($varGenSeg);
                                    $promocion->setNombreFiltro($varGenSeg);
                                    $promocion->setTipo(Promocion::TIPO_SEGMENTADA);
                                    $promocion->setEstado(1);
                                    $promocion->setNumPromocion($objNumPro[0]);
                                    $promocion->setAceptada(Promocion::PENDIENTE);
                                    $promocion->setTipo(Promocion::TIPO_SEGMENTADA);

                                    $this->em->merge($promocion);
                                }
                            }
                        }
                    }

                }


                //Creatividades genéricas

                $numGenericas = $grupoSlot['numGenericas'];
                if ($numGenericas > 0) {
                    $numGenericasSave = $grupoSlot['promCreatividadGenericas'];

                    //Ya creadas
                    if ($numGenericasSave > 0) {
                        $arrayIds = explode(",", $grupoSlot['idsPromoCrGenericas']);
                        foreach ($arrayIds as $idPC) {
                            $nomVarDesc = sprintf("gen_desc_%s_%s", $idGrupoSlots, $idPC);
                            $nomVarCre  = sprintf("gen_idcre_%s_%s", $idGrupoSlots, $idPC);

                            $varGenDesc = $request->get($nomVarDesc);
                            $varGenCre  = $request->get($nomVarCre);

                            $objPromCre = $this->em->getRepository('RMProductoBundle:Promocion')->find($idPC);

                            $objPromCre->setDescripcion($varGenDesc);
                            if (strlen($varGenCre) > 0) {
                                $objCre = $this->em->getRepository('RMComunicacionBundle:Creatividad')->find($varGenCre);
                                $objPromCre->setCreatividad($objCre);
                            }

                            $this->em->merge($objPromCre);
                        }
                    }

                    //Nuevas
                    $numGenericasNuevas = $numGenericas - $numGenericasSave;
                    if ($numGenericasNuevas > 0) {

                        //Se obtiene el objeto de Num_promociones asignado al grupo y a la instancia
                        $objNumPro = $this->em->getRepository('RMProductoBundle:NumPromociones')
                                              ->obtenerNumPromocionesCreatividadByFiltros($idGrupoSlots, $id_instancia)
                        ;

                        if (sizeof($objNumPro) > 0) {
                            for ($i = 1; $i <= $numGenericasNuevas; $i++) {
                                $nomVarDesc = sprintf("new_gen_desc_%s_%s", $idGrupoSlots, $i);
                                $nomVarCre  = sprintf("new_gen_idcre_%s_%s", $idGrupoSlots, $i);

                                //Si no esta relleno la descripción, no se guardará
                                $varGenDesc = $request->get($nomVarDesc);
                                if (strlen($varGenDesc) > 0) {
                                    $varGenCre = $request->get($nomVarCre);

                                    $objPromCre = new Promocion();

                                    $objPromCre->setDescripcion($varGenDesc);

                                    if (strlen($varGenCre) > 0) {
                                        $objCre = $this->em->getRepository('RMComunicacionBundle:Creatividad')->find($varGenCre);
                                        $objPromCre->setCreatividad($objCre);
                                    }

                                    $objPromCre->setTipo(Promocion::TIPO_GENERICA);
                                    $objPromCre->setEstado(1);
                                    $objPromCre->setNumPromocion($objNumPro[0]);
                                    $objPromCre->setAceptada(Promocion::PENDIENTE);
                                    $objPromCre->setTipo(Promocion::TIPO_GENERICA);

                                    $this->em->merge($objPromCre);
                                }
                            }
                        }
                    }

                }

            }

            $this->em->flush();
        } catch (\Exception $ex) {
            $respuesta = 0;
        }

        return $respuesta;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return array
     */
    public function getDatosPromocionesCreatividadByInstancia(InstanciaComunicacion $instancia)
    {
        if (!$instancia) {
            return [];
        }

        $numPromociones = $this->em
            ->getRepository('RMProductoBundle:NumPromociones')
            ->findNumPromocionesCreatividadByInstancia($instancia->getIdInstancia())
        ;


        $infoCreatividades = [];
        /** @var NumPromociones $numPro */
        foreach ($numPromociones as $numPro) {
            $idGrupo     = $numPro->getIdGrupo()->getIdGrupo();
            $idNumPro    = $numPro->getIdNumPro();
            $segmentadas = $numPro->getPromocionesSegmentadas()->toArray();
            $genericas   = $numPro->getPromocionesGenericas()->toArray();

            $infoCreatividades[$idGrupo][$idNumPro] = [
                'numPromocion' => $numPro,
                'segmentadas'  => $segmentadas,
                'genericas'    => $genericas
            ];
        }

        return $infoCreatividades;
    }

}