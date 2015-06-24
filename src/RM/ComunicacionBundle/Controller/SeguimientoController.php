<?php

namespace RM\ComunicacionBundle\Controller;

use RM\ProductoBundle\Entity\Promocion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SeguimientoController
 *
 * @package RM\ComunicacionBundle\Controller
 */
class SeguimientoController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('rm.manager')->getManager();

        if ($request->isXmlHttpRequest()) {
            $filtro = $request->query->get('filtro');
            $filtro = $this->resolveFilterOptions($filtro);

            $promociones = $promociones = $em->getRepository('RMProductoBundle:Promocion')
                ->obtenerPromocionesFiltradasPor(
                    $filtro['categoria'],
                    $filtro['producto'],
                    $filtro['marca'],
                    $filtro['desde'],
                    $filtro['hasta']);

            $data = [
                'data'            => $promociones,
                "recordsTotal"    => intval(count($promociones)),
                "recordsFiltered" => intval(count($promociones)),
            ];

            return JsonResponse::create($data, Response::HTTP_OK);
        }


        $promociones = $em->getRepository('RMProductoBundle:Promocion')
            ->obtenerPromocionesFiltradasPor();

        $categorias = array_column($promociones, 'idCategoria');
        $marcas     = array_column($promociones, 'idMarca');

        if(!empty($categorias)) {
            $categorias = $em->getRepository('RMCategoriaBundle:Categoria')
                ->findBy(['idCategoria' => $categorias]);
        }

        if(!empty($marcas)) {
            $marcas = $em->getRepository('RMProductoBundle:Marca')
                ->findBy(['idMarca' => $marcas]);
        }


        return $this->render('RMComunicacionBundle:Seguimiento:index.html.twig', [
            'categorias'    => $categorias,
            'marcas'        => $marcas,
        ]);
    }

    /**
     * Resuelve las opciones de búsqueda del filtro. Asigna valores null por defecto en caso
     * de no encontrar ningun valor para la opción.
     *
     * @param array $filtro
     *
     * @return array
     */
    protected function resolveFilterOptions($filtro = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'categoria' => null,
                'producto'  => null,
                'marca'     => null,
                'desde'     => null,
                'hasta'     => null
            ]
        );

        $resolver->setNormalizer('desde', function ($options, $value) {
            $value = !empty($value) ? \DateTime::createFromFormat('d/m/Y', $value) : null;

            return $value;
        });

        $resolver->setNormalizer('hasta', function ($options, $value) {
            $value = !empty($value) ? \DateTime::createFromFormat('d/m/Y', $value) : null;

            return $value;
        });

        $filtro = $resolver->resolve($filtro);

        return $filtro;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function obtenerProductosPorMarcaAction(Request $request)
    {

        $servicioProductos = $this->get('productoservice');

        $id_marca = $request->get('id_marca');

        if (!$id_marca) {
            return new JsonResponse();
        }

        $productos = $servicioProductos->getProductosByMarca($id_marca);

        return new JsonResponse($productos);

    }
}
