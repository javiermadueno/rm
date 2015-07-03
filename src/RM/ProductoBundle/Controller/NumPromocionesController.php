<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/06/2015
 * Time: 17:50
 */

namespace RM\ProductoBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Form\Type\NumPromocionesCollectionType;
use Symfony\Component\HttpFoundation\Request;

class NumPromocionesController extends RMController
{

    public function editAction(Request $request, $id_instancia, $id_grupo_slot)
    {
        $em = $this->getManager();
        $numPromociones = $em->getRepository('RMProductoBundle:NumPromociones')->findBy([
            'idInstancia' => $id_instancia,
            'idGrupo' => $id_grupo_slot
        ]);

        $form = $this->createForm(new NumPromocionesCollectionType(), ['num_promociones' => $numPromociones],[
            'em' => $em,
            'nivel_categoria' => $em->getRepository('RMDiscretasBundle:Configuracion')->findOneBy([
                'nombre' => 'nivel_category_manager'
            ])
        ]);

        $numPromocionesOriginales = new ArrayCollection($numPromociones);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data = $data['num_promociones'];
        }


    }

} 