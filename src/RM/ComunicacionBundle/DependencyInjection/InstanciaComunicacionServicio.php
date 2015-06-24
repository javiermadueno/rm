<?php

namespace RM\ComunicacionBundle\DependencyInjection;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\InstanciaCriterioDesempate;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\HttpFoundation\Request;

class InstanciaComunicacionServicio
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @var InstanciaComunicacionRepository
     */
    private $repository;


    public function __construct(DoctrineManager $manager)
    {
        $this->em = $manager->getManager();
        $this->repository = $this->em->getRepository('RMComunicacionBundle:InstanciaComunicacion');
    }

    public function getInstanciaById($id_instancia)
    {
        $registros = $this->repository->obtenerInstanciaById($id_instancia);

        return $registros;
    }

    public function getInstanciasByFiltro(
        $id_comunicacion = -1,
        $id_segmento = -1,
        $fase = -1,
        $fecha_inicio = -1,
        $fecha_fin = -1
    ) {

        $registros = $this->repository->obtenerInstanciasByFiltro($id_comunicacion, $id_segmento, $fase, $fecha_inicio,
            $fecha_fin);

        return $registros;
    }

    public function getInstanciasByFiltroDQL(
        $id_comunicacion = -1,
        $id_segmento = -1,
        $fase = -1,
        $fecha_inicio = -1,
        $fecha_fin = -1
    ) {
        $consulta = $this->repository->obtenerInstanciasByFiltroDQL($id_comunicacion, $id_segmento, $fase,
            $fecha_inicio, $fecha_fin);

        return $consulta;
    }

    public function guardarFaseConfPromocionesByPost(
        InstanciaComunicacion $objInstancia,
        $objGrupoSlots,
        $gruposCreatividades,
        $objCategorias,
        $objNumPromociones,
        $objPromocionesCreatividad,
        Request $request
    ) {

        $manager = $this->em;

        $categoriaYGrupoUsados = [];
        $grupoCreatividadesUsados = [];
        $grupoCreatividad = [];

        /**
         * Primero se comprueba si ha habido cambios en la num_promociones existentes
         * y se actualiza su valor
         */

        foreach ($objNumPromociones as $numPromo) {
            $idGrupoSlots = $numPromo->getIdGrupo()->getIdGrupo();
            $idCategoria = $numPromo->getIdCategoria()->getIdCategoria();

            $nomVarSeg = sprintf("seg_%s_%s", $idGrupoSlots, $idCategoria);
            $nomVarGen = sprintf("gen_%s_%s", $idGrupoSlots, $idCategoria);

            $varGen = $request->get($nomVarGen);
            $varSeg = $request->get($nomVarSeg);

            if ($varGen || $varSeg) {

                if (null != $varGen) {
                    $numPromo->setNumGenericas($varGen);
                }

                if (null != $varSeg) {
                    $numPromo->setNumSegmentadas($varSeg);
                }

                $manager->merge($numPromo);

                array_push($categoriaYGrupoUsados, sprintf("%s_%s", $idGrupoSlots, $idCategoria));
            }

        }

        foreach ($objPromocionesCreatividad as $numPromo) {
            $idGrupoSlots = $numPromo->getIdGrupo()->getIdGrupo();

            $nomVarSeg = sprintf("segCre_%s", $idGrupoSlots);
            $nomVarGen = sprintf("genCre_%s", $idGrupoSlots);
            array_push($grupoCreatividad, $idGrupoSlots);

            $varGen = $request->get($nomVarGen);
            $varSeg = $request->get($nomVarSeg);

            if ($varGen || $varSeg) {

                if (null != $varGen) {
                    $numPromo->setNumGenericas($varGen);
                }

                if (null != $varSeg) {
                    $numPromo->setNumSegmentadas($varSeg);
                }

                $manager->merge($numPromo);

                array_push($grupoCreatividadesUsados, sprintf("%s", $idGrupoSlots));
            }
        }

        $manager->flush();

        /**
         * Despues se recorren todos los posibles inputs que haya en el formulario. Si tienen valor
         * se genera un registro en num_promociones con los datos necesarios
         */

        foreach ($objGrupoSlots as $grupoSlot) {
            $idGrupoSlots = $grupoSlot['idGrupo'];

            //Se comprueba si el grupo es de tipo creatividad
            if ($grupoSlot['tipo'] == GrupoSlots::CREATIVIDADES) {
                if (in_array(sprintf("%s", $idGrupoSlots), $grupoCreatividadesUsados)) {
                    continue;
                }

                $nomVarSeg = sprintf("segCre_%s", $idGrupoSlots);
                $nomVarGen = sprintf("genCre_%s", $idGrupoSlots);

                $varGen = $request->get($nomVarGen);
                $varSeg = $request->get($nomVarSeg);

                if ($varGen || $varSeg) {

                    $objGrupo = $manager->getRepository('RMPlantillaBundle:GrupoSlots')->find($idGrupoSlots);
                    $numPromocion = new NumPromociones();
                    $numPromocion->setEstado(1);
                    $numPromocion->setIdGrupo($objGrupo);
                    $numPromocion->setIdInstancia($objInstancia);

                    if (null != $varSeg) {
                        $numPromocion->setNumSegmentadas($varSeg);
                    }

                    if (null != $varGen) {
                        $numPromocion->setNumGenericas($varGen);
                    }

                    $manager->persist($numPromocion);

                }
            } else {
                foreach ($objCategorias as $categoria) {


                    $idCategoria = $categoria->getIdCategoria();

                    //Si ya se ha comprobado los valores de esta categoria y grupo continua con el resto de elemtos
                    if (in_array(sprintf("%s_%s", $idGrupoSlots, $idCategoria), $categoriaYGrupoUsados)) {
                        continue;
                    }

                    $nomVarSeg = sprintf("seg_%s_%s", $idGrupoSlots, $idCategoria);
                    $nomVarGen = sprintf("gen_%s_%s", $idGrupoSlots, $idCategoria);

                    $varGen = $request->get($nomVarGen);
                    $varSeg = $request->get($nomVarSeg);

                    if ($varGen || $varSeg) {

                        $objGrupo = $manager->getRepository('RMPlantillaBundle:GrupoSlots')->find($idGrupoSlots);
                        $numPromocion = new NumPromociones();
                        $numPromocion->setEstado(1);
                        $numPromocion->setIdCategoria($categoria);
                        $numPromocion->setIdGrupo($objGrupo);
                        $numPromocion->setIdInstancia($objInstancia);


                        if (null != $varSeg) {
                            $numPromocion->setNumSegmentadas($varSeg);
                        }

                        if (null != $varGen) {
                            $numPromocion->setNumGenericas($varGen);
                        }

                        $manager->persist($numPromocion);

                    }
                }
            }
        }

        $manager->flush();
    }

    public function guardarCriteriosFaseConfiguracion(
        InstanciaComunicacion $objInstancia,
        $objGrupoSlots,
        $criteriosDesempate,
        $instanciasCriterios,
        $request
    ) {
        $manager = $this->em;

        $criteriosYGruposUsados = [];

        foreach ($instanciasCriterios as $instancia) {
            $idGrupo = $instancia->getGrupo()->getIdGrupo();
            $tipoCriterio = $instancia->getCriterioDesempate()->getCodigo();

            $varNumSlot = sprintf("numSlot_%s_%s", $idGrupo, $tipoCriterio);

            $numSlot = $request->get($varNumSlot);

            if (null != $numSlot) {
                $instancia->setNumSlot($numSlot);
                $manager->merge($instancia);

                array_push($criteriosYGruposUsados, sprintf("%s_%s", $idGrupo, $tipoCriterio));
            }
        }

        $manager->flush();

        foreach ($objGrupoSlots as $grupoSlot) {
            foreach ($criteriosDesempate as $criterio) {
                $idGrupo = $grupoSlot['idGrupo'];
                $tipoCriterio = $criterio->getCodigo();

                if (in_array(sprintf("%s_%s", $idGrupo, $tipoCriterio), $criteriosYGruposUsados)) {
                    continue;
                }

                $varNumSlot = sprintf("numSlot_%s_%s", $idGrupo, $tipoCriterio);

                $numSlot = $request->get($varNumSlot);

                if (null != $numSlot) {
                    $objGrupo = $manager->getRepository('RMPlantillaBundle:GrupoSlots')->find($idGrupo);

                    //Se crea una nueva instancia de criterio de Desempate
                    $instanciaCriterio = new InstanciaCriterioDesempate();
                    $instanciaCriterio->setGrupo($objGrupo);
                    $instanciaCriterio->setCriterioDesempate($criterio);
                    $instanciaCriterio->setIdInstancia($objInstancia);
                    $instanciaCriterio->setNumSlot($numSlot);

                    $manager->persist($instanciaCriterio);
                }
            }
        }

        $manager->flush();
    }

    public function getResumenPromocionesByTipo($id_instancia)
    {
        $consulta = $this->repository->obtenerResumenPromocionesByTipo($id_instancia);

        return $consulta;
    }

    public function getResumenPromocionesByEstado($id_instancia)
    {
        $consulta = $this->repository->obtenerResumenPromocionesByEstado($id_instancia);

        return $consulta;
    }

    public function getCampanyasByFiltro($id_categoria)
    {
        $registros = $this->repository->obtenerCampanyasByFiltro($id_categoria);

        return $registros;
    }

    public function getClosingCampanyas()
    {
        $registros = $this->repository->obtenerClosingCampaigns();

        return $registros;
    }

    public function getCierreCampanyasByFiltro($id_categoria)
    {
        $registros = $this->repository->obtenerClosingCampaigns($id_categoria);

        return $registros;
    }

    public function getCategoryManagersByBusinessCategory($businessCategory)
    {
        $bind_rdn = 'cn=admin,dc=relationalmessages,dc=com';
        $bind_password = 'admin';
        $host = '192.168.100.229';

        $client = [
            'port'              => 389,
            'host'              => $host,
            'version'           => '3',
            'referrals_enabled' => null,
            'username'          => $bind_rdn,
            'password'          => $bind_password,
            'base_dn'           => null,
            'filter'            => null,
            'name_attribute'    => ""
        ];

        $params = [
            'user'    => $client,
            'base_dn' => "",
            'filter'  => ""
        ];

        // ConexiÃ³n
        $data = ldap_connect($host);

        if ($data) {

            // Bind
            ldap_set_option($data, LDAP_OPT_PROTOCOL_VERSION, 3);
            $data2 = ldap_bind($data, $bind_rdn, $bind_password);

            if ($data2) {
                // Query users. Todos los usuarios con la businessCategory
                $dn = "ou=usuarios,dc=relationalmessages,dc=com";
                $filter = "(businessCategory=*$businessCategory*)";
                $justthese = [
                    "uid",
                    "givenName",
                    "mail",
                    "telephoneNumber"
                ];

                $params ['base_dn'] = $dn;
                $params ['filter'] = $filter;
                $params ['attrs'] = $justthese;

                // Search
                $usersCategory = ldap_search($data, $dn, $filter, $justthese);

                // Getting results
                $infoUsersCategory = ldap_get_entries($data, $usersCategory);

                // Query Category Managers. Todos los members del ROLE Category Manager
                $dn2 = "ou=roles,dc=relationalmessages,dc=com";
                $filter2 = "(cn=category_manager)";
                $justthese2 = [
                    "member"
                ];

                $params ['base_dn'] = $dn2;
                $params ['filter'] = $filter2;
                $params ['attrs'] = $justthese2;

                // Search
                $sr = ldap_search($data, $dn2, $filter2, $justthese2);

                // Getting results
                $infoUsersCategoryManagers = ldap_get_entries($data, $sr);

                $arrayUsersAvisos = [];

                if (is_array($infoUsersCategoryManagers)
                    && $infoUsersCategoryManagers ['count'] > 0
                    && is_array($infoUsersCategory)
                    && $infoUsersCategory ['count'] > 0
                ) {

                    for ($i = 0; $i < $infoUsersCategoryManagers ['count']; $i++) {

                        $dnCM = $infoUsersCategoryManagers [0] ['member'] [$i];

                        for ($j = 0; $j < $infoUsersCategory ['count']; $j++) {

                            $dnUC = $infoUsersCategory [$j] ['dn'];
                            $givenName = $infoUsersCategory [$j] ['givenname'];
                            $mail = $infoUsersCategory [$j] ['mail'];
                            $telephoneNumber = $infoUsersCategory [$j] ['telephonenumber'];

                            if ($dnCM = $dnUC) {

                                $var = [
                                    $businessCategory,
                                    $givenName,
                                    $mail,
                                    $telephoneNumber
                                ];

                                array_push($arrayUsersAvisos, $var);
                            }
                        }
                    }
                }

                return $arrayUsersAvisos;
            } else {

                return 0;
            }
        }
    }


    //CREATIVIDADES

    public function getInstanciasCreatividad()
    {
        $registros = $this->repository->obtenerInstanciasCreatividad();

        return $registros;
    }

    public function findGruposSlotsByInstancia($idInstancia)
    {
        $dql = "
            SELECT DISTINCT gs
            FROM RMComunicacionBundle:InstanciaComunicacion ic WITH (ic.idInstancia = :idInstancia)
            JOIN RMComunicacionBunle:SegmentoComunicacion sc WITH  (sc.idInstancia = ic.idInstancia AND sc.estado > -1)
            JOIN RMComunicacionBundle:Comununicacion c WITH (sc.idComunicacion = c.idComunicacion AND c.estado> -1)
            JOIN RMPlantillaBundle:Plantilla p WITH (c.plantilla = p.idPlantilla AND p.estado> -1)
            JOIN RMplantillaBundle:GrupoSlots gs WITH (gs.idPlantilla = p.idPlantilla)
            WHERE gs.estado > -1
        ";

        $query = $this->em->createQuery($dql);
        $query->setParameter('idInstancia', $idInstancia);
        $res = $query->getResult();

        return $res;
    }

    /**
     * @param int $id_instancia
     *
     * @return bool
     */
    public function cambioFase($id_instancia = 0)
    {
        $instancia = $this->repository->find($id_instancia);

        if (!$instancia instanceof InstanciaComunicacion) {
            return false;
        }

        $fase = $instancia->getFase()->getCodigo();

        switch ($fase) {
            case InstanciaComunicacion::FASE_CONFIGURACION:
                return $this->tramitarACampanya($instancia);
                break;
            case InstanciaComunicacion::FASE_NEGOCIACION:
                return $this->tramitarASimulacion($instancia);
                break;
            case InstanciaComunicacion::FASE_CIERRE:
                return $this->tramitarAGeneracion($instancia);
            default:
                return false;
        }
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarACampanya(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseConfiguracion($instancia)) {
            return false;
        }

        $faseCampanya = $this->em->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_NEGOCIACION]);


        return $this->cambiaFase($instancia, $faseCampanya);
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseConfiguracion(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CONFIGURACION) {
            return false;
        }

        /**
         * Se comprueba que por cada GrupoSlots de la plantilla haya un registro en num_promociones
         */
        $grupoSlots = $this
            ->findNumRegistrosNumPromocionesPorGrupoSlotsByIdInstancia($instancia->getIdInstancia());

        foreach ($grupoSlots as $grupoSlot) {
            if (!intval($grupoSlot['numPro'])) {
                return false;
            }
        }

        /**
         * Se comprueba que el total de genericas por grupo de slots sea igual o superior al numero de slots del grupo.
         */
        $totalGenericasPorgrupo = $this->em->getRepository('RMProductoBundle:NumPromociones')
            ->findTotalGenericasPorGrupoByInstancia($instancia->getIdInstancia());


        foreach ($totalGenericasPorgrupo as $total) {
            $totalGenericas = $total['totalGenericas'];
            $totalSlots = $total['totalSlots'];

            if ($totalGenericas < $totalSlots) {
                return false;
            }
        }

        return true;

    }

    public function findNumRegistrosNumPromocionesPorGrupoSlotsByIdInstancia($idInstancia)
    {
        $dql = "
            SELECT gs.idGrupo as idGrupoSlot , COUNT( DISTINCT np.idNumPro ) as numPro
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (sc.idSegmentoComunicacion = ic.idSegmentoComunicacion AND sc.estado > -1)
            JOIN  RMComunicacionBundle:Comunicacion c WITH (c.idComunicacion = sc.idComunicacion AND c.estado > -1)
            JOIN RMPlantillaBundle:Plantilla p WITH(c.plantilla = p.idPlantilla AND p.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots  gs WITH (gs.idPlantilla = p.idPlantilla AND gs.estado > -1)
            LEFT JOIN RMProductoBundle:NumPromociones np WITH(np.idGrupo = gs.idGrupo AND np.idInstancia = ic.idInstancia)
            WHERE ic.idInstancia = :idInstancia
            AND ic.estado > -1
            GROUP BY gs.idGrupo
            ORDER BY gs.idGrupo
        ";

        $query = $this->em->createQuery($dql);
        $query->setParameter('idInstancia', $idInstancia);
        $res = $query->getResult();

        return $res;

    }

    /**
     * @param InstanciaComunicacion $instancia
     * @param Fases                 $fase
     *
     * @return bool
     */
    private function cambiaFase(InstanciaComunicacion $instancia, Fases $fase)
    {
        try {
            $instancia
                ->setFase($fase)
                ->setEstado(1);

            $this->em->persist($instancia);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarASimulacion(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseCampanya($instancia)) {
            return false;
        }

        $faseSimulacion = $this->em->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_SIMULACION]);

        return $this->cambiaFase($instancia, $faseSimulacion);
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseCampanya(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_NEGOCIACION) {
            return false;
        }

        /**
         * Se comprueba que el número de promociones creadas sea el indicado en numPromociones
         */

        $numPromociones = $instancia->getNumPromociones();
        foreach ($numPromociones as $numPromocion) {
            $totalSegmentadas = intval($numPromocion->getNumSegmentadas());
            $totalGenericas = intval($numPromocion->getNumGenericas());

            $segmentadas = $numPromocion->getPromocionesSegentadas()->count();
            $genericas = $numPromocion->getPromocionesGenericas()->count();

            if ($genericas < $totalGenericas) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarAGeneracion(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseCierre($instancia)) {
            return false;
        }

        $faseGeneracion = $this->em->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_GENERACION]);

        if ($this->compruebaPromocionesRechazadasEnFaseCierre($instancia)) {
            $instancia->setPaso(InstanciaComunicacion::PASO_1);

            return $this->cambiaFase($instancia, $faseGeneracion);
        }

        $instancia->setPaso(InstanciaComunicacion::PASO_2);

        return $this->cambiaFase($instancia, $faseGeneracion);
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseCierre(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CIERRE) {
            return false;
        }

        /**
         * Se comprueba que no haya ninguna promoción pendiente
         *
         * @var NumPromociones $numPro
         * @var Promocion      $promocion
         */
        foreach ($instancia->getNumPromociones() as $numPro) {
            foreach ($numPro->getPromociones() as $promocion) {
                if ($promocion->getAceptada() == Promocion::PENDIENTE) {
                    return false;
                }
            }
        }

        return true;
    }

    public function compruebaPromocionesRechazadasEnFaseCierre(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CIERRE) {
            return false;
        }

        /**
         * Comprueba si hay promociones rechazadas
         *
         * @var NumPromociones $numPro
         * @var Promocion      $promocion
         */
        foreach ($instancia->getNumPromociones() as $numPro) {
            foreach ($numPro->getPromociones() as $promocion) {
                if ($promocion->getAceptada() == Promocion::RECHAZADA) {
                    return true;
                }
            }
        }

        return false;
    }
}
