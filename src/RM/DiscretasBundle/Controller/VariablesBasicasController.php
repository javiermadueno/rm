<?php

namespace RM\DiscretasBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\DiscretasBundle\Entity\Configuracion;
use RM\DiscretasBundle\Entity\VidCriterioGlobal;
use RM\DiscretasBundle\Entity\VidSegmentoGlobal;
use RM\DiscretasBundle\Form\ConfiguracionCollectionType;
use RM\DiscretasBundle\Form\Type\VidCriteriosGlobalesCollectionType;
use RM\DiscretasBundle\Form\VariablesBasicas\ModificarCriteriosNyMType;
use RM\DiscretasBundle\Form\VidSegmentosGlobalesCollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VariablesBasicasController extends Controller
{
    public function showConfiguracionAction(Request $request, $idOpcionMenuSup, $idOpcionMenuIzq)
    {
        return $this->render('RMDiscretasBundle:VariablesBasicas:index.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
        ]);
    }

    public function criteriosEligibilidadAction(Request $request)
    {
        $em       = $this->get('rm.manager')->getManager();

        $criterios = $em->getRepository('RMDiscretasBundle:VidCriterioGlobal')->findAll();

        $formulario = $this->createForm(new VidCriteriosGlobalesCollectionType(), ['criterios' => $criterios], [
            'action' => $this->generateUrl('rm_discretas_bundle.variables_basicas.criterios'),
            'method' => 'post'
        ]);
        $formulario->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            if ($formulario->isValid()) {
                $em->flush();

                return JsonResponse::create([
                    'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                    'error'   => 0
                ], Response::HTTP_OK);
            }

            return JsonResponse::create([
                'mensaje' => $this->get('translator')->trans('mensaje.error.actualizar'),
                'error'   => 1
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('RMDiscretasBundle:Citerios:edit.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }


    public function segmentosAction(Request $request)
    {
        $em       = $this->get('rm.manager')->getManager();
        $servicio = $this->get("variablesDiscretas");

        $objSegmentosPre = $servicio->getSegmentosGlobales();

        $formSegmentos = $this->createForm(new VidSegmentosGlobalesCollectionType(),
            ['segmentos' => new ArrayCollection($objSegmentosPre)],
            [
                'action' => $this->generateUrl('rm_discretas_bundle.variables_basicas.segmentos'),
                'method' => 'post'
            ]);

        $segmentosOriginales = new ArrayCollection($objSegmentosPre);

        if ($request->isXmlHttpRequest()) {
            $formSegmentos->handleRequest($request);
            if ($formSegmentos->isValid()) {

                $segmentosNuevos = $formSegmentos->getData();

                /** @var ArrayCollection $segmentosNuevos */
                $segmentosNuevos = $segmentosNuevos['segmentos'];

                /** @var VidSegmentoGlobal $segmento */
                foreach ($segmentosNuevos as $segmento) {
                    if (false === $segmentosOriginales->contains($segmento)) {
                        $segmento->setEstado(1);
                        $em->persist($segmento);
                    }
                }

                /** @var VidSegmentoGlobal $segmentoOriginal */
                foreach ($segmentosOriginales as $segmentoOriginal) {
                    if (false === $segmentosNuevos->contains($segmentoOriginal)) {
                        $segmentoOriginal->setEstado(-1);
                        $em->remove($segmentoOriginal);
                    }
                }

                $em->flush();

                return JsonResponse::create([
                    'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                    'form' => $this->renderView('@RMDiscretas/Segmentos/body_form_segmentos.html.twig',
                        ['formSegmentos' => $formSegmentos->createView()]),
                    'error'   => 0
                ], Response::HTTP_OK);
            }

            return JsonResponse::create([
                'mensaje' => $this->get('translator')->trans('mensaje.error.actualizar'),
                'form' => $this->renderView('@RMDiscretas/Segmentos/body_form_segmentos.html.twig',
                    ['formSegmentos' => $formSegmentos->createView()]),
                'error'   => 1
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('RMDiscretasBundle:Segmentos:edit.html.twig', [
            'formSegmentos' => $formSegmentos->createView()
        ]);


    }

    public function parametrosConfiguracionAction(Request $request)
    {
        $em = $this->get('rm.manager')->getManager();

        $parametros = $em
            ->getRepository('RMDiscretasBundle:Configuracion')
            ->findParametrosConfiguracionByTipo(Configuracion::SEGMENTOS);

        $form = $this->createForm(new ConfiguracionCollectionType(), ['parametros' => $parametros], [
            'method' => 'POST',
            'action' => $this->generateUrl('rm_discretas_bundle.variables_basicas.parametros_configuracion')
        ]);


        if(!$request->isXmlHttpRequest()) {
            return $this->render('RMDiscretasBundle:Configuracion:edit.html.twig', ['form' => $form->createView()]);
        }

        $form->handleRequest($request);

        if($form->isValid()) {
             $em->flush();

            return JsonResponse::create([
                'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                'form' => $this->renderView('@RMDiscretas/Configuracion/body_form_configuracion.html.twig',
                    ['form' => $form->createView()]),
                'error'   => 0
            ], Response::HTTP_OK);
        }

        return JsonResponse::create([
            'mensaje' => $this->get('translator')->trans('mensaje.error.actualizar'),
            'form' => $this->renderView('@RMDiscretas/Configuracion/body_form_configuracion.html.twig',
                ['form' => $form->createView()]),
            'error'   => 1
        ], Response::HTTP_BAD_REQUEST);


    }




}