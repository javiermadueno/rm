<?php

namespace RM\DiscretasBundle\Controller;

use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoNType;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoNyMType;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HabitosCompraController extends Controller
{

    public function editAction(Request $request, $id_vid)
    {
        $em       = $this->get('rm.manager')->getManager();
        $repo     = $em->getRepository('RMDiscretasBundle:Vid');
        $servicio = $this->get('variablesdiscretas');

        $variable = $repo->findById($id_vid);

        if (!$variable instanceof Vid) {
            $this->createNotFoundException(sprintf("No se ha encontrado la variable %s", $id_vid));
        }

        $vidGrupoSegmento = $servicio->getGSbyIdVid($variable);
        $segmentos        = $servicio->getSegmentosByIdGrupo($vidGrupoSegmento->getIdVidGrupoSegmento());
        $criterioGlobal   = $servicio->getCriteriosGlobales()[0];

        if ($vidGrupoSegmento->getPersonalizado() && $variable->getSolicitaTiempo() === Vid::SOLICITA_N) {
            $form = $this->createForm(new ModificarGrupoType(), $vidGrupoSegmento);
        } elseif ($variable->getSolicitaTiempo() === Vid::SOLICITA_N) {
            $form = $this->createForm(new ModificarGrupoNType(), $criterioGlobal);
        } else {
            $form = $this->createForm(new ModificarGrupoNyMType(), $criterioGlobal);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'mensaje.ok.editar');
        }

        return $this->render('RMDiscretasBundle:HabitosCompra:edit.html.twig', [
            'objVariable'      => $variable,
            'form'             => $form->createView(),
            'segmentos'        => $segmentos,
            'grupoVidSegmento' => $vidGrupoSegmento
        ]);
    }

}
