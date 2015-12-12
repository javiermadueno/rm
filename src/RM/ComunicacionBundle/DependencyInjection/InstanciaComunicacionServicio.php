<?php

namespace RM\ComunicacionBundle\DependencyInjection;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\CategoriaBundle\Entity\Categoria;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Entity\InstanciaComunicacionRepository;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\CriterioDesempate;
use RM\ProductoBundle\Entity\InstanciaCriterioDesempate;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\HttpFoundation\Request;

class InstanciaComunicacionServicio
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var InstanciaComunicacionRepository
     */
    private $repository;


    public function __construct(DoctrineManager $manager)
    {
        $this->em         = $manager->getManager();
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



    public function guardarCriteriosFaseConfiguracion(
        InstanciaComunicacion $objInstancia,
        $objGrupoSlots,
        $criteriosDesempate,
        $instanciasCriterios,
        Request $request
    ) {
        $manager = $this->em;

        $criteriosYGruposUsados = [];

        /** @var InstanciaCriterioDesempate $instancia */
        foreach ($instanciasCriterios as $instancia) {
            $idGrupo      = $instancia->getGrupo()->getIdGrupo();
            $tipoCriterio = $instancia->getCriterioDesempate()->getCodigo();

            $varNumSlot = sprintf("numSlot_%s_%s", $idGrupo, $tipoCriterio);

            $numSlot = $request->get($varNumSlot);

            if (null !== $numSlot) {
                $instancia->setNumSlot($numSlot);
                $manager->merge($instancia);

                array_push($criteriosYGruposUsados, sprintf("%s_%s", $idGrupo, $tipoCriterio));
            }
        }

        $manager->flush();

        foreach ($objGrupoSlots as $grupoSlot) {

            /** @var CriterioDesempate $criterio */
            foreach ($criteriosDesempate as $criterio) {
                $idGrupo      = $grupoSlot['idGrupo'];
                $tipoCriterio = $criterio->getCodigo();

                if (in_array(sprintf("%s_%s", $idGrupo, $tipoCriterio), $criteriosYGruposUsados)) {
                    continue;
                }

                $varNumSlot = sprintf("numSlot_%s_%s", $idGrupo, $tipoCriterio);

                $numSlot = $request->get($varNumSlot);

                if (null !== $numSlot) {
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
        $bind_rdn      = 'cn=admin,dc=relationalmessages,dc=com';
        $bind_password = 'admin';
        $host          = '192.168.100.229';

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
                $dn        = "ou=usuarios,dc=relationalmessages,dc=com";
                $filter    = "(businessCategory=*$businessCategory*)";
                $justthese = [
                    "uid",
                    "givenName",
                    "mail",
                    "telephoneNumber"
                ];

                $params ['base_dn'] = $dn;
                $params ['filter']  = $filter;
                $params ['attrs']   = $justthese;

                // Search
                $usersCategory = ldap_search($data, $dn, $filter, $justthese);

                // Getting results
                $infoUsersCategory = ldap_get_entries($data, $usersCategory);

                // Query Category Managers. Todos los members del ROLE Category Manager
                $dn2        = "ou=roles,dc=relationalmessages,dc=com";
                $filter2    = "(cn=category_manager)";
                $justthese2 = [
                    "member"
                ];

                $params ['base_dn'] = $dn2;
                $params ['filter']  = $filter2;
                $params ['attrs']   = $justthese2;

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

                            $dnUC            = $infoUsersCategory [$j] ['dn'];
                            $givenName       = $infoUsersCategory [$j] ['givenname'];
                            $mail            = $infoUsersCategory [$j] ['mail'];
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



    public function getInstanciasCreatividad()
    {
        $registros = $this->repository->obtenerInstanciasCreatividad();

        return $registros;
    }


}
