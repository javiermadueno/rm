<?php

namespace RM\ComunicacionBundle\Controller;

use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;
use RM\ProductoBundle\Form\PromocionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CampaignController extends Controller
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

    public function showCampanyasAction($idOpcionMenuSup, $idOpcionMenuIzq, $closing, $id_categoria = 0)
    {
        $servicioIC  = $this->get("InstanciaComunicacionService");
        $servicioCat = $this->get("categoriaservice");

        if ($id_categoria == -1) {
            $objInstancias = null;
        } else {
            $objInstancias = $servicioIC->getCampanyasbyFiltro($id_categoria);
        }

        $objCategorias = $servicioCat->getCategoriasDeCampanya();


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

    public function actualizarListadoCampanyasAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request     = $this->container->get('request');
            $servicioIC  = $this->get("InstanciaComunicacionService");
            $idCategoria = $request->get('id_categoria', 0);
            $servicioCat = $this->get("CategoriaService");

            $objCategorias = $servicioCat->getCategoriasDeCampanya();
            $objInstancias = $servicioIC->getCampanyasbyFiltro($idCategoria);

            return $this->render(
                'RMComunicacionBundle:Campaign\Negociaciones:tablaListadoCampanyas.html.twig',
                [
                    'objInstancias' => $objInstancias,
                    'id_categoria'  => $request->get('id_categoria'),
                    'closing'       => $request->get('closing'),
                    'objCategorias' => $objCategorias
                ]
            );
        } else {

            $request    = $this->container->get('request');
            $servicioIC = $this->get("InstanciaComunicacionService");

            if ($request->get('id_categoria') == -1) {
                $objInstancias = null;
            } else {
                $objInstancias = $servicioIC->getCampanyasbyFiltro($request->get('id_categoria'));
            }

            $servicioCat   = $this->get("CategoriaService");
            $objCategorias = $servicioCat->getCategoriasDeCampanya();


            return $this->render(
                'RMComunicacionBundle:Campaign\Negociaciones:tablaListadoCampanyas.html.twig',
                [
                    'objInstancias' => $objInstancias,
                    'id_categoria'  => $request->get('id_categoria'),
                    'objCategorias' => $objCategorias
                ]
            );
        }
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

            $em = $this->getDoctrine()->getManager($_SESSION['connection']);

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
                $promociones[$idGrupo][$idNumPro]['segmentadas']  = $numPromocion->getPromocionesSegentadas()->toArray();
                $promociones[$idGrupo][$idNumPro]['genericas']    = $numPromocion->getPromocionesGenericas()->toArray();
                $promociones[$idGrupo][$idNumPro]['marcas']       = $servicioMarca->getMarcasByCategoria($idCategoria);
            }

            if (!empty($gruposSlot)) {
                $gruposSlot = $em->getRepository('RMPlantillaBundle:GrupoSlots')->findBy(
                    [
                        'idGrupo' => $gruposSlot
                    ]
                );
            }

            $tipoPromocion = $em->getRepository('RMProductoBundle:TipoPromocion')->findAll();
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

            $em = $this->getDoctrine()->getManager($_SESSION['connection']);

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
                $promociones[$idGrupo][$idNumPro]['segmentadas']  = $numPromocion->getPromocionesSegentadas()->toArray();
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

        $objProductos = $this->get('rm.manager')->getManager()->getRepository('RMProductoBundle:Producto')
            ->findProductosByCategoriaYMarca($categoria, $marca);

        return new JsonResponse($objProductos);
    }

    public function fichaPromocionAction(Request $request, $id_promocion)
    {
        $em = $this->get('rm.manager')->getManager();

        $promocion = $em->getRepository('RMProductoBundle:Promocion')->findBydId($id_promocion);

        if (!$promocion instanceof Promocion) {
            return $this->createNotFoundException(sprintf('No se ha encontrado promocion con id "%s"', $id_promocion));
        }

        $form = $this->createForm(new PromocionType(), $promocion,
            [
                'method' => 'post',
                'action' => $this->generateUrl('campaign_ficha_promocion_guardar', ['id' => $id_promocion])
            ]
        );


        return $this->render('RMComunicacionBundle:Campaign\Negociaciones:fichaPromocion.html.twig',
            [
                'promocion' => $promocion,
                'producto'  => $promocion->getIdProducto(),
                'form'      => $form->createView()
            ]
        );
    }

    public function infoPromocionAction($id_promocion)
    {
        $servicioPr     = $this->get("PromocionService");
        $objPromociones = $servicioPr->getPromocionById($id_promocion);
        if (!$objPromociones) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        } else {
            $objPromocion = $objPromociones [0];

            return $this->render(
                'RMComunicacionBundle:Campaign\Negociaciones:infoPromocion.html.twig',
                [
                    'objPromocion' => $objPromocion
                ]
            );
        }
    }

    public function guardarFichaPromocionAction(Request $request, $id)
    {
        $em        = $this->get('rm.manager')->getManager();
        $promocion = $em->getRepository('RMProductoBundle:Promocion')->findBydId(($id));

        if (!$promocion instanceof Promocion) {
            return $this->createNotFoundException(sprintf('No se ha encontrado promocion con id "%s"', $id));
        }

        $form = $this->createForm(new PromocionType(), $promocion,
            [
                'method' => 'post',
                'action' => $this->generateUrl('campaign_ficha_promocion_guardar', ['id' => $id])
            ]
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'mensaje.editar.ok');
            $response = $this->render('::logMensajes.html.twig');

            return $response;
        }

        return JsonResponse::create($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    public function guardaPoblacionyFiltroPromocionAction()
    {
        $request = $this->get('request');

        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException();
        }

        $idPromocion  = $request->get('idPromocion');
        $poblacion    = $request->get('poblacion');
        $nombreFiltro = $request->get('nombreFiltro');
        $condicion    = $request->get('condicion');

        $em   = $this->get('doctrine')->getManager($_SESSION['connection']);
        $repo = $em->getRepository('RMProductoBundle:Promocion');

        $promocion = $repo->find($idPromocion);

        if (!$promocion) {
            return $this->createNotFoundException();
        }

        $promocion
            ->setPoblacion($poblacion)
            ->setNombreFiltro($nombreFiltro)
            ->setFiltro($condicion);

        $em->persist($promocion);
        $em->flush();

        $respuesta = [
            'mensaje' => sprintf('Con el filtro especificado se ha calculado una poblacion de %s clientes', $poblacion)
        ];

        return new  JsonResponse($respuesta);
    }

    public function saveCampaignSlotsAction()
    {

        $servicioPromocion = $this->get('PromocionService');

        $request = $this->container->get('request');

        $promociones  = $request->get('promocion');
        $id_instancia = $request->get('id_instancia');
        $id_categoria = $request->get('id_categoria');

        $promociones = $this->compruebaPromociones($promociones);


        if (empty ($promociones)) {
            $this->get('session')->getFlashBag()->add('mensaje', 'No se ha realizado ninguna modificación.');

            return $this->redirect(
                $this->generateUrl(
                    'campaign_ficha',
                    [
                        'id_instancia' => $id_instancia,
                        'id_categoria' => $id_categoria
                    ]
                )
            );
        }

        $respuesta = $servicioPromocion->guardarPromocionesCampanya($promociones);

        if ($respuesta == 1) {
            $this->get('session')->getFlashBag()->add('mensaje', 'Inserciones realizadas correctamente!.');
        } else {
            $this->get('session')->getFlashBag()->add('mensaje', 'Ha habido algún problema al guardar los datos.');
        }

        return $this->redirect(
            $this->generateUrl(
                'campaign_ficha',
                [
                    'id_instancia' => $id_instancia,
                    'id_categoria' => $id_categoria
                ]
            )
        );
    }

    public function compruebaPromociones(array $promociones)
    {
        if (empty($promociones)) {
            return false;
        }

        foreach ($promociones as $idNumPro => $promocion) {
            if (isset($promocion['segmentadas'])) {
                foreach ($promocion['segmentadas'] as $indice => $segmentada) {
                    if ($this->isNullOrEmpty($segmentada['tipo'])
                        || $this->isNullOrEmpty($segmentada['minimo'])
                        || $this->isNullOrEmpty($segmentada['producto'])
                    ) {
                        unset($promociones[$idNumPro]['segmentadas'][$indice]);
                    }
                }
            }
            if (isset($promocion['genericas'])) {

                foreach ($promocion['genericas'] as $indice => $generica) {
                    if ($this->isNullOrEmpty($generica['tipo'])
                        || $this->isNullOrEmpty($generica['producto'])
                    ) {
                        unset($promociones[$idNumPro]['genericas'][$indice]);
                    }
                }
            }

        }

        return $promociones;
    }

    public function isNullOrEmpty($variable)
    {
        return empty($variable) || $variable == '-1' || $variable == -1 ? true : false;
    }

    public function saveCampaignClosingSlotsAction()
    {

        $request           = $this->container->get('request');
        $servicioPromocion = $this->get("PromocionService");
        $data              = $request->get('promociones');
        $id_instancia      = $request->request->get('id_instancia');
        $id_categoria      = $request->request->get('id_categoria');

        if (empty ($data)) {

            $this->get('session')->getFlashBag()->add('mensaje', 'No se ha realizado ninguna modificación.');

            return $this->redirect(
                $this->generateUrl(
                    'campaign_closing_ficha',
                    [
                        'id_instancia' => $id_instancia,
                        'id_categoria' => $id_categoria
                    ]
                )
            );
        }

        $respuesta = $servicioPromocion->actualizarPromocionesCampanya($data);
        if ($respuesta == 1) {
            $this->get('session')->getFlashBag()->add('mensaje', 'Inserciones realizadas correctamente!.');

        } else {
            $this->get('session')->getFlashBag()->add('mensaje', 'Ha habido algún problema al guardar los datos');

        }

        return $this->redirect(
            $this->generateUrl(
                'campaign_closing_ficha',
                [
                    'id_instancia' => $id_instancia,
                    'id_categoria' => $id_categoria
                ]
            )
        );

    }
}