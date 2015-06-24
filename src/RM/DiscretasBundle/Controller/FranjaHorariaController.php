<?php

namespace RM\DiscretasBundle\Controller;

use RM\DiscretasBundle\Form\FranjasHorariasCollectionForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FranjaHorariaController extends Controller
{
    public function editAction(Request $request)
    {
        $em = $this->get('rm.manager')->getManager();

        $franjas = $em->getRepository('RMDiscretasBundle:FranjaHoraria')->findAll();

        $form = $this->createForm(new FranjasHorariasCollectionForm(), ['franjas' => $franjas], [
            'action' => $this->generateUrl('rm_discretas_bundle.franja_horaria.edit'),
            'method' => 'post'
        ]);

        if ($request->isXmlHttpRequest()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();
                $response = [
                    'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                    'error'   => 0
                ];

                return JsonResponse::create($response, Response::HTTP_OK);
            }

            return JsonResponse::create([
                'mensaje' => $this->get('translator')->trans('mensaje.error.actualizar'),
                'error'   => 1
            ], 400);
        }

        return $this->render('@RMDiscretas/FranjaHoraria/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
