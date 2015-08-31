<?php

namespace RM\ComunicacionBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\CategoriaBundle\Entity\Categoria;
use RM\ComunicacionBundle\Entity\Campaign;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CampaignController extends RMController
{

    public function indexAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        return $this->render(
            'RMComunicacionBundle:Campaign\Negociaciones:index.html.twig',
            [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq
            ]
        );
    }

    public function showCampanyasAction(Request $request, $idOpcionMenuSup, $idOpcionMenuIzq, $closing)
    {
        $id_categoria = $request->query->getInt('id_categoria', 0);

        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioCat = $this->get("categoriaservice");

        $objCategorias             = $servicioCat->getCategoriasDeCampanya();
        $ids_categorias_permitidas = array_map(
            function(Categoria $categoria){
                return $categoria->getIdCategoria();
            }, $objCategorias
        );

        if($id_categoria === 0) {
            $objInstancias = $servicioIC->getCampanyasbyFiltro($ids_categorias_permitidas);
        }
        elseif (in_array($id_categoria, $ids_categorias_permitidas)) {
            $objInstancias = $servicioIC->getCampanyasbyFiltro($id_categoria);
        } else {
            $objInstancias = null;
        }

        return $this->render(
            'RMComunicacionBundle:Campaign\Negociaciones:listadoCampanyas.html.twig',
            [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'objInstancias'   => $objInstancias,
                'id_categoria'    => $id_categoria,
                'objCategorias'   => $objCategorias,
                'closing'         => $closing
            ]
        );
    }

    public function showClosingCampaignsAction($idOpcionMenuSup, $idOpcionMenuIzq, $id_categoria = 0)
    {
        $servicioIntanciaComunicacion = $this->get('instanciacomunicacionservice');
        $servicioCategorias           = $this->get('categoriaservice');

        if ($id_categoria === -1) {
            $objInstancias = null;
        } else {
            $objInstancias = $servicioIntanciaComunicacion->getClosingCampanyas();
        }

        $objCategorias = $servicioCategorias->getCategoriasDeCampanya();


        return $this->render(
            'RMComunicacionBundle:Campaign\Negociaciones:listadoCampanyas.html.twig',
            [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'objInstancias'   => $objInstancias,
                'id_categoria'    => $id_categoria,
                'objCategorias'   => $objCategorias,
                'closing'         => 1

            ]
        );
    }

    public function fichaCampanyaAction($idOpcionMenuSup, $idOpcionMenuIzq, $id_instancia, $id_categoria)
    {
        $servicioIC = $this->get("instanciacomunicacionservice");

        $objInstancias = $servicioIC->getInstanciaById($id_instancia);
        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {

            /**
             * Refactorizacion del metodo para mostrar la ficha de una campaÃ±a
             */
            $servicioMarca = $this->get("marcaservice");

            $em = $this->getManager();

            $objInstancia = $objInstancias [0];

            //Si esta definida la categoria se buscan las numPromociones asociadas a esta categoria
            //En caso contrario se buscan todas asociadas a las instancia de comunicacion
            if ($id_categoria) {
                $numPromociones = $em->getRepository('RMProductoBundle:NumPromociones')
                    ->findNumPromocionesByInstanciayCategoria($id_instancia, $id_categoria);

            } else {
                $numPromociones = $em->getRepository('RMProductoBundle:NumPromociones')
                    ->findNumPromocionesByInstancia($id_instancia);
            }

            //Categorias de la instancia dependiendo del rol del usuario logueado.
            $categorias = $this->get('categoriaservice')->getCatByInstancia($id_instancia);

            $gruposSlot = [];

            //Este array contendrá toda la informacion para representar en la plantilla
            $promociones = [];
            /** @var NumPromociones $numPromocion */
            foreach ($numPromociones as $numPromocion) {
                $idCategoria = $numPromocion->getIdCategoria();

                if (!in_array($idCategoria, $categorias)) {
                    continue;
                }

                $idNumPro = $numPromocion->getIdNumPro();
                $idGrupo  = $numPromocion->getIdGrupo()->getIdGrupo();

                $gruposSlot[] = $idGrupo;

                $promociones[$idGrupo][$idNumPro]['numPromocion'] = $numPromocion;
                $promociones[$idGrupo][$idNumPro]['segmentadas']  = $numPromocion->getPromocionesSegmentadas()->toArray();
                $promociones[$idGrupo][$idNumPro]['genericas']    = $numPromocion->getPromocionesGenericas()->toArray();
                $promociones[$idGrupo][$idNumPro]['marcas']       = $servicioMarca->getMarcasByCategoria($idCategoria);
            }

            if (!empty($gruposSlot)) {
                $gruposSlot = $em->getRepository('RMPlantillaBundle:GrupoSlots')
                    ->findBy(['idGrupo' => $gruposSlot]);
            }

            $tipoPromocion = $em->getRepository('RMProductoBundle:TipoPromocion')
                ->findAll();
            $categoria     = $em->find('RMCategoriaBundle:Categoria', $id_categoria);


            return $this->render(
                'RMComunicacionBundle:Campaign/Negociaciones:fichaCampanyas.html.twig',
                [
                    'gruposSlot'      => $gruposSlot,
                    'promociones'     => $promociones,
                    'tipoPromocion'   => $tipoPromocion,
                    'objInstancia'    => $objInstancia,
                    'categoria'       => $categoria,
                    'idOpcionMenuSup' => $idOpcionMenuSup,
                    'idOpcionMenuIzq' => $idOpcionMenuIzq,
                    'categorias'      => $categorias
                ]
            );
            //-----===== FIN REFACTORIZACION ===== --------
        }
    }

    public function showAction(Request $request, $id_instancia)
    {
        $em = $this->getManager();

        $instancia = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->findById($id_instancia);

        if (!$instancia instanceof InstanciaComunicacion) {
            throw $this->createNotFoundException(sprintf(
                "No existe la Instancia con id = %s",
                $id_instancia
            ));
        }

        $categorias = $this
            ->get('categoriaservice')
            ->getCatByInstancia($instancia->getIdInstancia());

        $campaign = new Campaign($instancia, new ArrayCollection($categorias));

        return $this->render('RMStaticBundle:Default:campaign.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    public function fichaClosingCampanyaAction($idOpcionMenuSup, $idOpcionMenuIzq, $id_instancia, $id_categoria)
    {
        $servicioIC    = $this->get("InstanciaComunicacionService");
        $objInstancias = $servicioIC->getInstanciaById($id_instancia);
        if (!$objInstancias) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {

            /**
             * Refactorizacion del metodo para mostrar la ficha de un cierre de campaña
             */

            $em = $this->getManager();

            $objInstancia = $objInstancias [0];

            //Si esta definida la categoria se buscan las numPromociones asociadas a esta categoria
            //En caso contrario se buscan todas asociadas a las instancia de comunicacion
            if ($id_categoria) {
                $numPromociones = $em->getRepository('RMProductoBundle:NumPromociones')
                    ->findNumPromocionesByInstanciayCategoria($id_instancia, $id_categoria);

            } else {
                $numPromociones = $em->getRepository('RMProductoBundle:NumPromociones')
                    ->findNumPromocionesByInstancia($id_instancia);
            }


            $categorias = $this->get('categoriaservice')->getCatByInstancia($id_instancia);
            $gruposSlot = [];

            //Este array contendrá toda la informacion para representar en la plantilla
            $promociones = [];

            /** @var  NumPromociones $numPromocion */
            foreach ($numPromociones as $numPromocion) {
                $idCategoria = $numPromocion->getIdCategoria();

                if (!in_array($idCategoria, $categorias)) {
                    continue;
                }

                $idNumPro = $numPromocion->getIdNumPro();
                $idGrupo  = $numPromocion->getIdGrupo()->getIdGrupo();

                $gruposSlot[] = $idGrupo;

                $promociones[$idGrupo][$idNumPro]['numPromocion'] = $numPromocion;
                $promociones[$idGrupo][$idNumPro]['segmentadas']  = $numPromocion->getPromocionesSegmentadas()->toArray();
                $promociones[$idGrupo][$idNumPro]['genericas']    = $numPromocion->getPromocionesGenericas()->toArray();
            }

            if (!empty($gruposSlot)) {
                $gruposSlot = $em->getRepository('RMPlantillaBundle:GrupoSlots')->findBy(
                    [
                        'idGrupo' => $gruposSlot
                    ]
                );
            }

            $categoria = $em->find('RMCategoriaBundle:Categoria', $id_categoria);

            return $this->render(
                'RMComunicacionBundle:Campaign/Negociaciones:fichaClosingCampaign.html.twig',
                [
                    'idOpcionMenuSup' => $idOpcionMenuSup,
                    'idOpcionMenuIzq' => $idOpcionMenuIzq,
                    'objInstancia'    => $objInstancia,
                    'categoria'       => $categoria,
                    'gruposSlots'     => $gruposSlot,
                    'promociones'     => $promociones
                ]
            );
        }
    }

    public function fichaCampanyaActualizarAction(Request $request)
    {
        $marca     = $request->request->get('idMarca');
        $categoria = $request->request->get('idCategoria');

        $objProductos = $this->get('rm.manager')
            ->getManager()
            ->getRepository('RMProductoBundle:Producto')
            ->findProductosByCategoriaYMarca($categoria, $marca);

        return new JsonResponse($objProductos);
    }


}