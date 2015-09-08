<?php

namespace RM\PlantillaBundle\Form;

use Doctrine\ORM\EntityRepository;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\TamanyoImagen;
use RM\PlantillaBundle\Form\DataTransformer\PlantillaToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GrupoSlotsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $plantillaTransformer = new PlantillaToNumberTransformer($em);
        $builder
            ->add('nombre', 'text')
            ->add('tipo', 'choice', [
                'choices'  => [
                    GrupoSlots::CREATIVIDADES => 'Creatividades',
                    GrupoSlots::PROMOCION     => 'Promocion'

                ],
                'required' => true
            ])
            ->add('estado', 'hidden', ['data' => 1])
            ->add('numSlots', 'integer', ['required' => true])
            ->add($builder->create('idPlantilla', 'hidden')->addModelTransformer($plantillaTransformer));

        $modificaTamanyoImagen = function (FormInterface $form, $tipo) use ($em) {

            $tamanyo = GrupoSlots::PROMOCION == $tipo ?
                TamanyoImagen::PRODUCTO:
                TamanyoImagen::CREATIVIDAD;

            $empty_value = TamanyoImagen::CREATIVIDAD === $tamanyo ?
                '- Tamaño Slot Creatividad -' :
                '- Tamaño Slot Promocion  -';

            $form->add('idTamanyoImgProducto', 'entity', [
                'class'         => 'RM\PlantillaBundle\Entity\TamanyoImagen',
                'em'            => $em,
                'query_builder' => function (EntityRepository $er) use ($tamanyo) {
                    return $er->createQueryBuilder('t')
                        ->where('t.tipo = :tipo')
                        ->andWhere('t.estado > -1')
                        ->setParameter('tipo', $tamanyo);
                },
                'property'      => 'codigo',
                'empty_value'   => $empty_value,
                'empty_data'    => null,
                'required'      => true,
                'label'         => 'tamano.slot.slot'
            ]);
        };

        $camposPromocion = function (FormInterface $form) {
            $form
                ->add('mImgProducto', 'checkbox', [
                    'required' => false,
                    'label'    => 'mostrar.imagen.producto'
                ])
                ->add('mPrecio', 'checkbox', [
                    'required' => false
                ])
                ->add('mVolumen', 'checkbox', [
                    'required' => false
                ])
                ->add('mCondiciones', 'checkbox', [
                    'required' => false
                ])
                ->add('mImgMarca', 'checkbox', [
                    'required' => false
                ])
                ->add('mTexto', 'checkbox', [
                    'required' => false,
                    'label' => 'mostrar.texto.libre'
                ])
                ->add('mVoucher', 'checkbox', [
                    'required' => false
                ])
                ->add('mFidelizacion', 'checkbox', [
                    'required' => false
                ]);
        };

        $camposCreatividad = function (FormInterface $form) {
            $form
                ->remove('mPrecio')
                ->remove('mVolumen')
                ->remove('mCondiciones')
                ->remove('mImgMarca')
                ->remove('mVoucher')
                ->remove('mFidelizacion')
            ;
            $form
                ->add('mImgProducto', 'checkbox', [
                    'required' => false,
                    'label'    => 'mostrar.imagen.creatividad'
                ])
                ->add('mTexto', 'checkbox', [
                    'required' => false,
                    'label'    => 'mostrar.texto.creatividad'
                ]);

        };

        $modificaCampos = function (FormInterface $form, $tipo) use ($camposPromocion, $camposCreatividad){

           if(GrupoSlots::PROMOCION == $tipo) {
               $camposPromocion($form);
           }else{
               $camposCreatividad($form);
           }
        };

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($modificaCampos, $modificaTamanyoImagen) {
            $grupo = $event->getData();
            $form  = $event->getForm();

            $modificaCampos($form, $grupo->getTipo());
            $modificaTamanyoImagen($form, $grupo->getTipo());

        });

        $builder->get('tipo')
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($modificaCampos, $modificaTamanyoImagen){
            $tipo = $event->getForm()->getData();
            $form  = $event->getForm()->getParent();

            $modificaCampos($form, $tipo);
            $modificaTamanyoImagen($form, $tipo);
        });


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\PlantillaBundle\Entity\GrupoSlots',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
        ])->setDefaults([
            'em' => null
        ])->addAllowedTypes([
            'em' => 'Doctrine\Common\Persistence\ObjectManager'
        ]);

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_plantillabundle_gruposlots';
    }
}
