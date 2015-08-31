<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 09/07/2015
 * Time: 16:05
 */

namespace RM\ProductoBundle\Controller;


use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;
use RM\ProductoBundle\Form\PromocionType;
use RM\ProductoBundle\Form\Type\NumPromocionesCampaignType;
use RM\ProductoBundle\Form\Type\PromocionGenericaCampanaType;
use RM\ProductoBundle\Form\Type\PromocionSegmentadaCampanaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PromocionController
 *
 * @package RM\ProductoBundle\Controller
 */
class PromocionController extends RMController
{
    /**
     * @param Request $request
     * @param         $id_promocion
     *
     * @return Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Exception
     */
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
        )
        ;


        return $this->render('RMComunicacionBundle:Campaign\Negociaciones:fichaPromocion.html.twig',
            [
                'promocion' => $promocion,
                'producto'  => $promocion->getIdProducto(),
                'form'      => $form->createView()
            ]
        )
            ;
    }

    /**
     * @param $id_promocion
     *
     * @return Response
     */
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
            )
                ;
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return Response|static
     * @throws \Exception
     */
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
        )
        ;

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'mensaje.editar.ok');
            $response = $this->render('::logMensajes.html.twig');

            return $response;
        }

        return JsonResponse::create($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function guardaPoblacionyFiltroPromocionAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $idPromocion  = $request->get('idPromocion');
        $poblacion    = $request->get('poblacion');
        $nombreFiltro = $request->get('nombreFiltro');
        $condicion    = $request->get('condicion');

        $em   = $this->getManager();
        $repo = $em->getRepository('RMProductoBundle:Promocion');

        $promocion = $repo->find($idPromocion);

        if (!$promocion instanceof Promocion) {
            throw $this->createNotFoundException();
        }

        $promocion
            ->setPoblacion($poblacion)
            ->setNombreFiltro($nombreFiltro)
            ->setFiltro($condicion)
        ;

        $em->persist($promocion);
        $em->flush();

        $respuesta = [
            'mensaje' => $this->get('translator')->trans('mensaje.calculo.poblacion',
                ['%poblacion%' => number_format($poblacion, 0, ',', '.')])
        ];

        return new  JsonResponse($respuesta);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveCampaignSlotsAction(Request $request)
    {

        $servicioPromocion = $this->get('PromocionService');

        $promociones  = $request->get('promocion');
        $id_instancia = $request->get('id_instancia');
        $id_categoria = $request->get('id_categoria');

        $promociones = $this->compruebaPromociones($promociones);

        if (empty ($promociones)) {
            $this->addFlash('mensaje', 'mensaje.error.editar');

            return $this->redirectToRoute('campaign_ficha', [
                'id_instancia' => $id_instancia,
                'id_categoria' => $id_categoria
            ])
                ;
        }

        $respuesta = $servicioPromocion->guardarPromocionesCampanya($promociones, $request->getLocale());

        if ($respuesta === 1) {
            $this->addFlash('mensaje', 'mensaje.ok.editar');
        } else {
            $this->addFlash('mensaje', 'mensaje.error.editar');
        }

        return $this->redirectToRoute('campaign_ficha', [
            'id_instancia' => $id_instancia,
            'id_categoria' => $id_categoria
        ])
            ;
    }

    /**
     * @param array $promociones
     *
     * @return array
     */
    private function compruebaPromociones(array $promociones)
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

    /**
     * @param $variable
     *
     * @return bool
     */
    private function isNullOrEmpty($variable)
    {
        return empty($variable) || $variable === '-1' || $variable === -1 ? true : false;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveCampaignClosingSlotsAction(Request $request)
    {
        $servicioPromocion = $this->get("PromocionService");
        $data              = $request->get('promociones');
        $id_instancia      = $request->request->get('id_instancia');
        $id_categoria      = $request->request->get('id_categoria');

        if (empty ($data)) {

            $this->addFlash('mensaje', 'No se ha realizado ninguna modificación.');

            return $this->redirectToRoute('campaign_closing_ficha', [
                'id_instancia' => $id_instancia,
                'id_categoria' => $id_categoria
            ])
                ;
        }

        $respuesta = $servicioPromocion->actualizarPromocionesCampanya($data);
        if ($respuesta === 1) {
            $this->addFlash('mensaje', 'Inserciones realizadas correctamente!.');

        } else {
            $this->addFlash('mensaje', 'Ha habido algún problema al guardar los datos');

        }

        return $this->redirectToRoute('campaign_closing_ficha', [
            'id_instancia' => $id_instancia,
            'id_categoria' => $id_categoria
        ])
            ;

    }

    /**
     * @param Request $request
     * @param         $idNumPromocion
     *
     * @return Response
     */
    public function promocionesByNumPromocionAction(Request $request, $idNumPromocion)
    {
        $em           = $this->getManager();
        $numPromocion = $em
            ->getRepository('RMProductoBundle:NumPromociones')
            ->findOneBy(['idNumPro' => $idNumPromocion])
        ;

        if (!$numPromocion instanceof NumPromociones) {
            throw $this->createNotFoundException(
                sprintf('No se ha encontrado numPromocion con id = "%s"', $idNumPromocion)
            )
            ;
        }

        $segmentadasOriginal = $numPromocion->getSegmentadas();

        $form = $this->createForm(new NumPromocionesCampaignType(), $numPromocion, ['em' => $em]);

        $form = $form->handleRequest($request);
        if ($form->isValid()) {

        }

        return $this->render('RMProductoBundle:Promocion:formulario.html.twig', [
            'num_promocion' => $numPromocion,
            'form'          => $form->createView()
        ])
            ;
    }

    /**
     * @param Request $request
     * @param         $idNumPromocion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newSegmentadaAction(Request $request, $idNumPromocion)
    {
        $em = $this->getManager();

        $numPromocion = $em
            ->getRepository('RMProductoBundle:NumPromociones')
            ->find($idNumPromocion)
        ;

        if (!$numPromocion instanceof NumPromociones) {
            return $this->createNotFoundException(
                sprintf('No se ha encontrado la NumPromocion con id = "%s"', $idNumPromocion)
            )
                ;
        }

        if ($numPromocion->isSegementadasCompletas()) {
            return $this->redirectToRoute('campaign_ficha',
                ['id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()])
                ;
        }


        $promocion = new Promocion();
        $promocion
            ->setNumPromocion($numPromocion)
            ->setTipo(Promocion::TIPO_SEGMENTADA)
            ->setAceptada(Promocion::PENDIENTE)
            ->setEstado(1)
        ;

        $form = $this->createForm(new PromocionSegmentadaCampanaType(), $promocion, [
            'em' => $em,
        ])
        ;

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($promocion);
            $em->flush();

            return $this->redirectToRoute('campaign_ficha',
                ['id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()])
                ;
        }

        return $this->render('RMProductoBundle:Promocion:form_segmentadas.html.twig', [
            'form'      => $form->createView(),
            'promocion' => $promocion
        ])
            ;
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editSegmentadaAction(Request $request, $id)
    {
        $em = $this->getManager();

        $promocion = $em->getRepository('RMProductoBundle:Promocion')->find($id);

        $form = $this->createForm(new PromocionSegmentadaCampanaType(), $promocion, [
            'em' => $em
        ])
        ;

        $form->add('submit', 'submit', ['label' => 'boton.editar']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('campaign_ficha', [
                'id_instancia' => $promocion->getNumPromocion()->getIdInstancia()->getIdInstancia()
            ])
                ;
        }

        return $this->render('@RMProducto/Promocion/form_segmentadas.html.twig', [
            'form'      => $form->createView(),
            'promocion' => $promocion
        ])
            ;
    }

    public function newGenericaAction(Request $request, $idNumPromocion)
    {
        $em = $this->getManager();

        $numPromocion = $em
            ->getRepository('RMProductoBundle:NumPromociones')
            ->find($idNumPromocion)
        ;

        if (!$numPromocion instanceof NumPromociones) {
            return $this->createNotFoundException(
                sprintf('No se ha encontrado la NumPromocion con id = "%s"', $idNumPromocion)
            )
                ;
        }

        if ($numPromocion->isGenericasCompletas()) {
            return $this->redirectToRoute('campaign_ficha',
                ['id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()])
                ;
        }

        $promocion = new Promocion();
        $promocion
            ->setNumPromocion($numPromocion)
            ->setTipo(Promocion::TIPO_GENERICA)
            ->setEstado(1)
            ->setAceptada(Promocion::PENDIENTE)
        ;

        $form = $this->createForm(new PromocionGenericaCampanaType(), $promocion, [
            'em' => $em
        ])
        ;

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($promocion);
            $em->flush();

            return $this->redirectToRoute('campaign_ficha',
                ['id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()])
                ;
        }

        return $this->render('@RMProducto/Promocion/form_generica.html.twig', [
            'form'      => $form->createView(),
            'promocion' => $promocion
        ])
            ;

    }

    public function editGenericaAction(Request $request, $id)
    {
        $em = $this->getManager();

        $promocion = $em->getRepository('RMProductoBundle:Promocion')->find($id);

        $form = $this->createForm(new PromocionGenericaCampanaType(), $promocion, [
            'em' => $em
        ])
        ;

        $form->add('submit', 'submit', ['label' => 'boton.editar']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('campaign_ficha', [
                'id_instancia' => $promocion->getNumPromocion()->getIdInstancia()->getIdInstancia()
            ])
                ;
        }

        return $this->render('@RMProducto/Promocion/form_generica.html.twig', [
            'form'      => $form->createView(),
            'promocion' => $promocion
        ])
            ;
    }

} 