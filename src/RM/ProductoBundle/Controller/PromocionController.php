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
use RM\ProductoBundle\Form\Type\PromocionCreatividadType;
use RM\ProductoBundle\Form\Type\PromocionGenericaCampanaType;
use RM\ProductoBundle\Form\Type\PromocionSegmentadaCampanaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @param $id_promocion
     *
     * @return Response
     */
    public function infoPromocionAction($id_promocion)
    {
        $em = $this->getManager();

        $promocion = $em
            ->getRepository('RMProductoBundle:Promocion')
            ->find($id_promocion);

        if (! $promocion instanceof Promocion) {
            throw $this->createNotFoundException('No se ha encontrado promocion');
        }

        return $this->render(
            'RMComunicacionBundle:Campaign\Negociaciones:infoPromocion.html.twig',
            [
                'objPromocion' => $promocion
            ]
        );
    }

    /**
     * @return Response|static
     */
    public function getUniqueVoucherAction()
    {
        $voucher = $this
            ->get('promocion.voucher.generator')
            ->generateUniqueVoucher()
        ;

        return JsonResponse::create([
            'voucher' => $voucher
        ])
            ;
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
     *
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
        if ($respuesta == 1) {
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

            if (! empty($url = $request->query->get('returnUrl'))) {
                return new RedirectResponse($url);
            }

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

            if (! empty($url = $request->query->get('returnUrl'))) {
                return new RedirectResponse($url);
            }

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

    /**
     * @param Request $request
     * @param         $idNumPromocion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
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
            ->setAceptada(Promocion::ACEPTADA)
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

            if (! empty($url = $request->query->get('returnUrl'))) {
                return new RedirectResponse($url);
            }

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

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
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

            if (! empty($url = $request->query->get('returnUrl'))) {
                return new RedirectResponse($url);
            }

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

    /**
     * @param Request $request
     * @param         $idNumPromocion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newCreatividadSegmentadaAction(Request $request, $idNumPromocion)
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
            return $this->redirectToRoute('rm_comunicacion.campaign.show_campaing_creatividades', [
                'id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()
            ]);
        }

        $promocion = new Promocion();
        $promocion
            ->setNumPromocion($numPromocion)
            ->setTipo(Promocion::TIPO_SEGMENTADA)
            ->setEstado(1)
            ->setAceptada(Promocion::ACEPTADA)
        ;

        $form = $this->createForm(new PromocionCreatividadType(), $promocion, [
            'em' => $em,
            'method' => Request::METHOD_POST
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em->persist($promocion);
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.campaign.show_campaing_creatividades', [
                'id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()
            ]);
        }

        return $this->render('@RMProducto/Promocion/form_creatividad_segmentada.html.twig', [
            'promocion' => $promocion,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @param         $idNumPromocion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newCreatividadGenericaAction(Request $request, $idNumPromocion)
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
            return $this->redirectToRoute('rm_comunicacion.campaign.show_campaing_creatividades', [
                'id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()
            ]);
        }

        $promocion = new Promocion();
        $promocion
            ->setNumPromocion($numPromocion)
            ->setTipo(Promocion::TIPO_GENERICA)
            ->setEstado(1)
            ->setAceptada(Promocion::ACEPTADA)
        ;

        $form = $this->createForm(new PromocionCreatividadType(), $promocion, [
            'em' => $em,
            'method' => Request::METHOD_POST
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em->persist($promocion);
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.campaign.show_campaing_creatividades', [
                'id_instancia' => $numPromocion->getIdInstancia()->getIdInstancia()
            ]);
        }

        return $this->render('@RMProducto/Promocion/form_creatividad_segmentada.html.twig', [
            'promocion' => $promocion,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editCreatividadAction(Request $request, $id)
    {
        $em = $this->getManager();

        $promocion = $em->getRepository('RMProductoBundle:Promocion')->find($id);

        $form = $this->createForm(new PromocionCreatividadType(), $promocion, [
            'em' => $em
        ])
        ;

        $form->add('submit', 'submit', ['label' => 'boton.editar']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.campaign.show_campaing_creatividades', [
                'id_instancia' => $promocion->getNumPromocion()->getIdInstancia()->getIdInstancia()
            ])
                ;
        }

        return $this->render('@RMProducto/Promocion/form_creatividad_segmentada.html.twig', [
            'form'      => $form->createView(),
            'promocion' => $promocion
        ])
            ;

    }

} 