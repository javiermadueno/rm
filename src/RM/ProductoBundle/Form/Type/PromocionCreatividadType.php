<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 01/09/2015
 * Time: 11:22
 */

namespace RM\ProductoBundle\Form\Type;


use Doctrine\ORM\EntityRepository;
use RM\ComunicacionBundle\Form\DataTransformer\CreatividadToStringTransformer;
use RM\ProductoBundle\Entity\Promocion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PromocionCreatividadType extends AbstractType
{

    public function getName()
    {
        return 'creatividad_segmentada';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $builder
            ->add('descripcion', 'textarea', [
                'required' => true,
                'label' => 'descripcion',
                'attr' => ['rows' => 4]
            ])
            ->add('filtro', 'hidden')
            ->add('creatividad', 'entity', [
                'class' => 'RMComunicacionBundle:Creatividad',
                'em' => $em,
                'required'=> true,
                'label' => 'creatividad',
                'property' => 'nombre',
                'empty_value' => null,
                'empty_data' => 'select.creatividad',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.estado > -1');
                }
            ])
           ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $form = $event->getForm();
            $promocion = $event->getData();

            if (!$promocion instanceof Promocion) {
                return;
            }

            if($promocion->getTipo() === Promocion::TIPO_SEGMENTADA) {
                $form
                    ->add('nombreFiltro', 'text', [
                        'required' => false,
                        'attr' => [
                            'readonly' => true
                        ]
                    ]);
            }
        });

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'RM\ProductoBundle\Entity\Promocion'
            ])
            ->setRequired([
                'em'
            ])
            ->setAllowedTypes([
                'em' => 'Doctrine\Common\Persistence\ObjectManager'
            ])
        ;
    }

} 