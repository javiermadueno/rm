<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 15/07/2015
 * Time: 14:27
 */

namespace RM\ProductoBundle\Form\Type;


use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use RM\CategoriaBundle\Entity\Categoria;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use RM\ProductoBundle\Entity\Marca;

class PromocionGenericaCampanaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data         = isset($options['data']) ? $options['data'] : null;

        if ($data instanceof Promocion ) {
            $numPromocion = $data instanceof Promocion ? $data->getNumPromocion() : null;
            $grupo = $numPromocion instanceof NumPromociones? $numPromocion->getIdGrupo(): null;
        }


        $builder
            ->add('descripcion', 'textarea', ['required' => (bool)$grupo->getMTexto(), 'label' => 'descripcion', 'attr' => ['rows' => 3]])
            ->add('impConsumidor', 'number', ['required' => false, 'label'=> 'imp.consumidor'])
            ->add('impDistribuidor', 'number', ['required' => false, 'label' => 'imp.distribuidor'])
            ->add('impFijo', 'number', ['required' => false, 'label' => 'imp.fijo'])
            ->add('condiciones', 'text', ['required' => (bool)$grupo->getMCondiciones(), 'label' => 'condiciones'])
            ->add('fidelizacion', 'text', ['required' => (bool)$grupo->getMFidelizacion(), 'label' => 'fidelizacion'])
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
            ->add('filtro', 'hidden', ['required' => false])
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
        return 'generica';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\Promocion'
        ])
                 ->setRequired(['em'])
                 ->setAllowedTypes(['em' => 'Doctrine\Common\Persistence\ObjectManager']);
    }

} 