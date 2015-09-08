<?php

namespace RM\ComunicacionBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\CategoriaBundle\Entity\Categoria;
use RM\ComunicacionBundle\Entity\Campaign;
use RM\ComunicacionBundle\Entity\CampaingCreatividad;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CampaignController extends RMController
{


    public function showCampanyasAction(Request $request, $idOpcionMenuSup, $idOpcionMenuIzq, $closing)
    {
        $id_categoria = $request->query->getInt('id_categoria', 0);

        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioCat = $this->get("categoriaservice");

        $objCategorias = $servicioCat->getCategoriasDeCampanya();
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

        if ($id_categoria == -1) {
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


    /**
     * @param Request $request
     * @param         $id_instancia
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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

        if($instancia->getFase()->getCodigo() !==  InstanciaComunicacion::FASE_NEGOCIACION) {
            return $this->redirectToRoute('campaign_campanyas');
        }

        $categorias = $this
            ->get('categoriaservice')
            ->getCatByInstancia($instancia->getIdInstancia());

        /**
         * La clase Campaign es un Decorator de InstanciaComunicacion
         */
        $campaign = new Campaign($instancia, new ArrayCollection($categorias));

        return $this->render('RMComunicacionBundle:Campaign/Negociaciones:campaign.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * @param Request $request
     * @param         $id_instancia
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showCampaignCreatividadesAction(Request $request, $id_instancia)
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

        if($instancia->getFase()->getCodigo() !==  InstanciaComunicacion::FASE_NEGOCIACION) {
            return $this->redirectToRoute('campaign_campanyas');
        }

        $creatividades = new CampaingCreatividad($instancia, new ArrayCollection());

        return $this->render('@RMComunicacion/Campaign/Creatividades/show.html.twig', [
            'campaign' => $creatividades
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


    public function listaCreatividadesAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        $servicioIC = $this->get('InstanciaComunicacionService');


        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(25); // Para poner el numero de item que se quieren por pagina

        $objInstancias = $paginator->paginate($servicioIC->getInstanciasCreatividad())->getResult();

        return $this->render(
            'RMComunicacionBundle:Campaign\Creatividades:listadoCreatividades.html.twig',
            [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'objInstancias'   => $objInstancias
            ]
        )
            ;
    }
}