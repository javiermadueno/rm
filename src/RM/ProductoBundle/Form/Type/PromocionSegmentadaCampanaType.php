<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 15/07/2015
 * Time: 14:27
 */

namespace RM\ProductoBundle\Form\Type;


use Doctrine\ORM\EntityRepository;
use RM\ProductoBundle\Entity\Marca;
use RM\ProductoBundle\Entity\Producto;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class PromocionSegmentadaCampanaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data         = isset($options['data']) ? $options['data'] : null;
        $numPromocion = $data instanceof Promocion ? $data->getNumPromocion() : null;

        $builder
            ->add('descripcion', 'text', ['required' => false, 'label' => 'descripcion'])
            ->add('impConsumidor', 'number', ['required' => false, 'label'=> 'imp.consumidor'])
            ->add('impDistribuidor', 'number', ['required' => false, 'label' => 'imp.distribuidor'])
            ->add('impFijo', 'number', ['required' => false, 'label' => 'imp.fijo'])
            ->add('condiciones', 'text', ['required' => false, 'label' => 'condiciones'])
            ->add('fidelizacion', 'text', ['required' => false, 'label' => 'fidelizacion'])
            ->add('codigo', 'text', ['required' => true, 'label' => 'codigo.promocion'])
            ->add('idTipoPromocion', 'entity', [
                'class'         => 'RMProductoBundle:TipoPromocion',
                'property'      => 'nombre',
                'required'      => true,
                'em'            => $options['em'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t');
                },
                'label'         => 'tipo.promocion'
            ])
            ->add('nombreFiltro', 'text', [
                'required' => false,
                'label' => 'segmentos',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('filtro', 'hidden', ['required' => false])
            ->add('poblacion', 'integer', ['required' => false, 'label' => 'poblacion', 'attr' => ['readonly' => true]])
            ->add('minimo', 'integer', ['required' => true, 'label' => 'minimo', 'constraints' => [new GreaterThan(0)]])
            ->add('marca', 'entity', [
                'required'      => true,
                'mapped'        => false,
                'class'         => 'RMProductoBundle:Marca',
                'empty_value'   => 'select.todas',
                'property'      => 'nombre',
                'em'            => $options['em'],
                'label'         => 'marca',
                'query_builder' => function (EntityRepository $er) use ($numPromocion) {

                    return
                        $er->createQueryBuilder('marca')
                           ->join('RMProductoBundle:Producto', 'producto', 'WITH',
                               'producto.idMarca = marca.idMarca')
                           ->where('producto.activo = 1')
                           ->andWhere('producto.idCategoria = :categoria')
                           ->orWhere('producto.idCategoria2 = :categoria')
                           ->orWhere('producto.idCategoria3 = :categoria')
                           ->orWhere('producto.idCategoria4 = :categoria')
                           ->orWhere('producto.idCategoria5 = :categoria')
                           ->orWhere('producto.idCategoria6 = :categoria')
                           ->orWhere('producto.idCategoria7 = :categoria')
                           ->orWhere('producto.idCategoria8 = :categoria')
                           ->orWhere('producto.idCategoria9 = :categoria')
                           ->orWhere('producto.idCategoria10 = :categoria')
                           ->orWhere('producto.idCategoria11 = :categoria')
                           ->setParameter('categoria', $numPromocion->getIdcategoria())
                        ;
                }
            ])
        ;

        $productoByMarcaYcategoria = function (Form $form, $marca, $categoria) use ($options) {

            $form->remove('idProducto');

            $form->add('idProducto', 'entity', [
                'required'      => true,
                'class'         => 'RMProductoBundle:Producto',
                'em'            => $options['em'],
                'property'      => 'nombre',
                'label'         => 'producto',
                'query_builder' => function (EntityRepository $er) use ($marca, $categoria) {
                    return $er->createQueryBuilder('producto')
                              ->where('producto.idMarca = :marca')
                              ->andWhere('producto.idCategoria = :categoria')
                              ->orWhere('producto.idCategoria2 = :categoria')
                              ->orWhere('producto.idCategoria3 = :categoria')
                              ->orWhere('producto.idCategoria4 = :categoria')
                              ->orWhere('producto.idCategoria5 = :categoria')
                              ->orWhere('producto.idCategoria6 = :categoria')
                              ->orWhere('producto.idCategoria7 = :categoria')
                              ->orWhere('producto.idCategoria8 = :categoria')
                              ->orWhere('producto.idCategoria9 = :categoria')
                              ->orWhere('producto.idCategoria10 = :categoria')
                              ->orWhere('producto.idCategoria11 = :categoria')
                              ->setParameter('categoria', $categoria)
                              ->setParameter('marca', $marca)
                        ;
                }
            ])
            ;
        };

        $builder->addEventListener(FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($productoByMarcaYcategoria) {
                $form = $event->getForm();
                $data = $event->getData();

                if ($data->getIdProducto() instanceof Producto) {
                    $marca = $data->getIdProducto()->getIdMarca();
                    $form->get('marca')->setData($marca);
                    $categoria = $data->getNumPromocion()->getIdCategoria();
                    $productoByMarcaYcategoria($form, $marca, $categoria);
                } else {
                    $form->add('idProducto', 'choice',
                        ['required'    => true,
                         'empty_value' => null,
                         'empty_data'  => 'select.marca',
                         'label'       => 'producto'
                        ])
                    ;
                }


            })
        ;

        $builder->get('marca')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($productoByMarcaYcategoria) {
                $form      = $event->getForm()->getParent();
                $marca     = $event->getData();
                $promocion = $form->getNormData();

                $categoria = $promocion instanceof Promocion ?
                    $promocion->getNumPromocion()->getIdCategoria() :
                    null;

                $productoByMarcaYcategoria($form, $marca, $categoria);
            })
        ;


    }

    public function getName()
    {
        return 'segmentada';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'RM\ProductoBundle\Entity\Promocion'
            ])
            ->setRequired(['em'])
            ->setAllowedTypes(['em' => 'Doctrine\Common\Persistence\ObjectManager'])
        ;
    }

} 