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