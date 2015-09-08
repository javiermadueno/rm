<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/06/2015
 * Time: 17:50
 */

namespace RM\ProductoBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use RM\AppBundle\Controller\RMController;
use RM\CategoriaBundle\Entity\Categoria;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Form\Type\NumPromocionesCollectionType;
use RM\ProductoBundle\Form\Type\NumPromocionesCreatividadType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NumPromocionesController extends RMController
{

    /**
     * @param Request $request
     * @param         $id_instancia
     * @param         $id_grupo_slot
     *
     * @return Response|static
     */
    public function editAction(Request $request, $id_instancia, $id_grupo_slot)
    {
        $em = $this->getManager();

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);
        $grupo     = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id_grupo_slot);

        $numPromociones           = $em->getRepository('RMProductoBundle:NumPromociones')->findBy([
            'idInstancia' => $id_instancia,
            'idGrupo'     => $id_grupo_slot
        ])
        ;

        $numPromocionesOriginales = new ArrayCollection($numPromociones);

        $form = $this->createEditForm($numPromociones, $id_instancia, $id_grupo_slot);

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->render('RMProductoBundle:NumPromociones:edit.html.twig', [
                'form'      => $form->createView(),
                'instancia' => $instancia,
                'grupo'     => $grupo
            ])
                ;
        }

        $handler = $this->get('rm_producto.num_promociones.form.handler');

        $handler
            ->setNumPromociones($numPromocionesOriginales)
            ->setInstancia($instancia)
            ->setGrupoSlot($grupo)
        ;

        try {
            if ($handler->handle($form, $request)) {
                return JsonResponse::create([
                    'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                    'form'    => $this->renderView('@RMProducto/NumPromociones/form_edit.html.twig',
                        ['form' => $form->createView()]),
                    'error'   => 0
                ], Response::HTTP_OK)
                    ;
            }
        } catch (DBALException $ex) {
            $this->handleException($form, $ex);
        }

        return JsonResponse::create([
            'mensaje' => $this->get('translator')->trans('mensaje.error.actualizar'),
            'form'    => $this->renderView('@RMProducto/NumPromociones/form_edit.html.twig',
                ['form' => $form->createView()]),
            'error'   => 1
        ], Response::HTTP_BAD_REQUEST)
            ;
    }

    /**
     * @param array $numPromociones
     * @param       $id_instancia
     * @param       $id_grupo_slot
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(array $numPromociones, $id_instancia, $id_grupo_slot)
    {
        $em = $this->getManager();

        $nivel_categoria = $em->getRepository('RMDiscretasBundle:Configuracion')->findOneBy([
            'nombre' => 'nivel_category_manager'
        ])
        ;

        $form = $this->createForm(new NumPromocionesCollectionType(),
            ['num_promocion' => new ArrayCollection($numPromociones)], [
                'action'          => $this->generateUrl('rm_producto.num_promociones.edit',
                    ['id_instancia' => $id_instancia, 'id_grupo_slot' => $id_grupo_slot]),
                'method'          => Request::METHOD_POST,
                'em'              => $em,
                'nivel_categoria' => $nivel_categoria->getValor()
            ])
        ;

        return $form;

    }

    /**
     * Agrega los errores correspondientes al formulario cuando se produce una excepcion al intentar
     * persistir en base de datos una entidad con la misma "categoria", "instancia" y "grupo_slot".
     *
     * @param FormInterface $form
     * @param DBALException $ex
     */
    private function handleException(FormInterface $form, DBALException $ex)
    {
        $state = $ex->getMessage();
        $state = strstr($state, 'SQLSTATE[');

        preg_match('/\d+-\d+-\d+/', $state, $matches);

        if(count($matches) > 0) {
            preg_match_all('/\d+/', $matches[0], $arguments);

            $id_categoria = isset($arguments[0][0]) ? intval($arguments[0][0]) : null;

            $elements = $form->get('num_promocion')->all();

            $num_promociones = array_filter($elements, function (FormInterface $form) use ($id_categoria) {

                $categoria = $form->get('idCategoria')->getData();
                if ($categoria instanceof Categoria) {
                    return $id_categoria === $categoria->getIdCategoria();
                }

                return false;
            });

            array_map(function (FormInterface $form) {
                $form->get('idCategoria')
                     ->addError(new FormError('No puede haber dos elementos con la misma categoria'))
                ;
            }, $num_promociones);
        }
    }

    /**
     * @param Request $request
     * @param         $id_instancia
     * @param         $id_grupo_slot
     *
     * @return Response|static
     * @throws \Exception
     */
    public function editCreatividadAction(Request $request, $id_instancia, $id_grupo_slot)
    {
        $em = $this->getManager();
        $manager = $this->get('rm_producto.num_promociones.manager');

        $instancia = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion')->findById($id_instancia);
        $grupo     = $em->getRepository('RMPlantillaBundle:GrupoSlots')->find($id_grupo_slot);

        if (GrupoSlots::CREATIVIDADES !== $grupo->getTipo()) {
            throw new \Exception('El Grupo de Slot seleccionado no es del tipo Creatividad');
        }

        $numPromocion = $em->getRepository('RMProductoBundle:NumPromociones')->findOneBy([
            'idInstancia' => $id_instancia,
            'idGrupo'     => $id_grupo_slot
        ])
        ;

        if (!$numPromocion instanceof NumPromociones) {
            $numPromocion = new NumPromociones();
            $numPromocion
                ->setIdInstancia($instancia)
                ->setIdGrupo($grupo);
        }

        $form = $this->createEditCreatividadForm($numPromocion, $id_instancia, $id_grupo_slot);

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->render('@RMProducto/NumPromociones/edit_creatividad.html.twig', [
                'form' => $form->createView(),
                'instancia' => $instancia,
                'grupo' => $grupo
            ]);
        }

        $form->handleRequest($request);
        if ($form->isValid()) {
            if(!$em->contains($numPromocion)){
                $manager->save($numPromocion);
            }
            $em->flush();

            return JsonResponse::create([
                'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
                'form'    => $this->renderView('@RMProducto/NumPromociones/form_creatividad_edit.html.twig',
                    ['form' => $form->createView()]),
                'error'   => 0
            ], Response::HTTP_OK)
                ;
        }

        return JsonResponse::create([
            'mensaje' => $this->get('translator')->trans('mensaje.ok.editar'),
            'form'    => $this->renderView('@RMProducto/NumPromociones/form_creatividad_edit.html.twig',
                ['form' => $form->createView()]),
            'error'   => 1
        ], Response::HTTP_BAD_REQUEST)
            ;

    }

    /**
     * @param NumPromociones $numPromocion
     * @param                $id_instancia
     * @param                $id_grupo_slot
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditCreatividadForm(NumPromociones $numPromocion, $id_instancia, $id_grupo_slot)
    {
        $form = $this->createForm(new NumPromocionesCreatividadType(), $numPromocion, [
            'em' => $this->getManager(),
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('rm_producto.num_promociones.edit_creatividad', [
                'id_instancia' => $id_instancia,
                'id_grupo_slot' => $id_grupo_slot
            ])
        ]);

        return $form;
    }

} 