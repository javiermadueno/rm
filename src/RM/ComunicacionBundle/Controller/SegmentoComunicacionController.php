<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 10/02/2015
 * Time: 17:47
 */

namespace RM\ComunicacionBundle\Controller;



use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use RM\ComunicacionBundle\Form\Type\SegmentoComunicacionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SegmentoComunicacionController extends RMController
{

    /**
     * @param $idComunicacion
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function indexAction($idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->find('RMComunicacionBundle:Comunicacion', $idComunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            return $this->createNotFoundException(
                sprintf("No se ha encontrado la comunicacion con Id = '%s'", $idComunicacion)
            );
        }

        $segmentos = $em->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->findSegmentosComunicacionByComunicacion($comunicacion);

        return $this->render('RMComunicacionBundle:SegmentoComunicacion:listado.html.twig', [
            'segmentos' => $segmentos,
            'comunicacion' => $comunicacion
        ]);
    }




    /**
     * @param Request $request
     * @param         $idComunicacion
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createAction(Request $request, $idComunicacion)
    {
        $em = $this->getManager();

        $comunicacion =
            $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            return $this->createNotFoundException(sprintf('La comunicacion con Id = "%s" no existe', $idComunicacion));
        }

        $segmentoComunicacion = new SegmentoComunicacion();
        $segmentoComunicacion->setIdComunicacion($comunicacion);

        $form = $this->createCreateForm($segmentoComunicacion);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($segmentoComunicacion);
            $em->flush();

            $this->addFlash('mensaje', 'mensaje.ok.crear');

            return $this->redirectToRoute(
                'direct_manager_edit_datos',
                ['idComunicacion' => $idComunicacion]
            );
        }


        return $this->render('RMComunicacionBundle:SegmentoComunicacion:new.html.twig', [
            'form'            => $form->createView(),
            'objComunicacion' => $comunicacion,
            'segmento'        => $segmentoComunicacion,
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getManager();

        $segmento = $em->getRepository('RMComunicacionBundle:SegmentoComunicacion')->findById($id);

        if(!$segmento instanceof SegmentoComunicacion ) {
            throw $this->createNotFoundException(sprintf(
                'No se ha encontrado el segmentoComunicacion con id "%s"',
                $id
            ));
        }

        $form  = $this->createEditForm($segmento);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $segmento->setProximaEjecucion(null);
            $segmento->getProximaEjecucion();
            $em->flush();

            $this->addFlash('mensaje', 'mensaje.ok.editar');
            return $this->redirectToRoute('direct_manager_edit_datos',
                ['idComunicacion' => $segmento->getIdComunicacion()->getIdComunicacion()]
            );
        }

        return $this->render('RMComunicacionBundle:SegmentoComunicacion:new.html.twig', [
            'form'            => $form->createView(),
            'objComunicacion' => $segmento->getIdComunicacion(),
            'segmento'        => $segmento,
        ]);

    }

    public function showAction(Request $request, $id)
    {
        $em = $this->getManager();
        $segmento = $em
            ->getRepository('RMComunicacionBundle:SegmentoComunicacion')
            ->find($id);

        if(!$segmento instanceof SegmentoComunicacion) {
            throw $this->createNotFoundException(sprintf('No se ha encontrado el segmento con id = "%s"', $id));
        }

        return $this->render('RMComunicacionBundle:SegmentoComunicacion:show.html.twig', [
            'segmento' => $segmento,
            'objComunicacion' => $segmento->getIdComunicacion(),
        ]);
    }

    /**
     * @param SegmentoComunicacion $segmentoComunicacion
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(SegmentoComunicacion $segmentoComunicacion)
    {
        $form = $this->createForm(new SegmentoComunicacionType(), $segmentoComunicacion, [
            'action' => $this->generateUrl('rm_comunicacion.segmento_comunicacion.new', [
                'idComunicacion' => $segmentoComunicacion->getIdComunicacion()->getIdComunicacion()
            ]),
            'method' => 'POST',
            'em'     => $this->getManager(),
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        return $form;
    }

    private function createEditForm(SegmentoComunicacion $segmentoComunicacion)
    {
        $form = $this->createForm(new SegmentoComunicacionType(), $segmentoComunicacion, [
            'action' => $this->generateUrl('rm_comunicacion.segmento_comunicacion.edit', [
                'id' => $segmentoComunicacion->getIdSegmentoComunicacion(),
                'idComunicacion' => $segmentoComunicacion->getIdComunicacion()->getIdComunicacion()
            ]),
            'method' => 'POST',
            'em' => $this->getManager()
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.actualizar']);

        return $form;
    }

    /**
     * @param Request $request
     * @param int     $idSegmentoComunicacion
     *
     * @return JsonResponse
     */
    public function reanudarSegmentoComunicacionAction(Request $request, $idSegmentoComunicacion = 0)
    {
        $translator = $this->get('translator');

        if (!$idSegmentoComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', Response::HTTP_BAD_REQUEST);
        }

        $segmentoComunicacion = $this->get('rm_comunicacion.cambia_estado_segmento_comunicacion')
            ->reanudarSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return Response::create($translator->trans('mensaje.error.reanudar.segmentocomunicacion'), Response::HTTP_BAD_REQUEST);
        }

        $mensaje = $translator->trans('mensaje.ok.segmentocomunicacion.reanudado');


        $html = $this->renderView('@RMComunicacion/SegmentoComunicacion/filaSegmentoComunicacion.html.twig',
            [
                'segmento' => $segmentoComunicacion
            ]
        );

        $respuesta = [
            'fila'    => $html,
            'mensaje' => $mensaje
        ];

        return new JsonResponse($respuesta);

    }

    /**
     * @param Request $request
     * @param int     $idSegmentoComunicacion
     *
     * @return JsonResponse
     */
    public function pararSegmentoComunicacionAction(Request $request, $idSegmentoComunicacion = 0)
    {
        $translator = $this->get('translator');

        if (!$idSegmentoComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', Response::HTTP_BAD_REQUEST);
        }

        $segmentoComunicacion = $this->get('rm_comunicacion.cambia_estado_segmento_comunicacion')
            ->pararSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return Response::create($translator->trans('mensaje.error.parar.segmentocomunicacion'), Response::HTTP_BAD_REQUEST);
        }

        $mensaje = $translator->trans('mensaje.ok.segmentocomunicacion.parado');


        $html = $this->renderView('@RMComunicacion/SegmentoComunicacion/filaSegmentoComunicacion.html.twig',
            [
                'segmento' => $segmentoComunicacion
            ]
        );

        $respuesta = [
            'fila'    => $html,
            'mensaje' => $mensaje
        ];

        return new JsonResponse($respuesta);
    }

    /**
     * @param Request $request
     * @param int     $idSegmentoComunicacion
     *
     * @return JsonResponse|Response
     */
    public function eliminarSegmentoComunicacionAction(Request $request, $idSegmentoComunicacion = 0)
    {
        $translator = $this->get('translator');

        if (!$idSegmentoComunicacion || !$request->isXmlHttpRequest()) {
            return Response::create('error', Response::HTTP_BAD_REQUEST);
        }

        $segmentoComunicacion = $this
            ->get('rm_comunicacion.cambia_estado_segmento_comunicacion')
            ->eliminarSegmentoComunicacion($idSegmentoComunicacion);

        if (!$segmentoComunicacion instanceof SegmentoComunicacion) {
            return Response::create($translator->trans('mensaje.error.eliminar'), Response::HTTP_BAD_REQUEST);
        }

        $mensaje = $translator->trans('mensaje.ok.segmentocomunicacion.eliminado');

        return new JsonResponse([
            'mensaje' => $mensaje
        ]);

    }
} 