<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/11/2015
 * Time: 11:49
 */

namespace RM\ComunicacionBundle\Form\Type;


use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstanciaOpcionesEmailType extends AbstractType
{




    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $instancia = $options['data'] instanceof InstanciaComunicacion ? $options['data'] : null;

        $attr = ['class' => 'datepicker'];
        $attr = $instancia->isEnvioInmediato() ? array_merge($attr, ['disabled'=> true]) : $attr;

       $builder
           ->add('asunto', 'text', ['required' => true])
           ->add('envioInmediato', 'choice', [
               'choices' => [
                   true => 'modulo.direct.fase.finalizada.envio.inmediato',
                   false => 'modulo.direct.fase.finalizada.envio.programado'
               ],
               'required' => true,
               'multiple' => false,
               'expanded' => true,
               'choice_value' => function ($currentChoiceKey) {
                   return $currentChoiceKey ? 'true' : 'false';
               },
           ])
           ->add('submit', 'submit', ['label' => 'boton.guardar'])
       ;


        $modificaFechaEnvio  =  function(FormEvent $event){
            $form = $event->getForm();
            $instancia = $form->getData();

            $attr = ['class' => 'datepicker'];
            $attr = $instancia->isEnvioInmediato() ? array_merge($attr, ['disabled'=> true]) : $attr;


            $form->add('fechaEnvio', 'date', [
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'widget' => 'single_text',
                'attr' => $attr
            ]);

        };


        $builder
            ->addEventListener(FormEvents::SUBMIT, $modificaFechaEnvio)
            ->addEventListener(FormEvents::POST_SET_DATA, $modificaFechaEnvio);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ComunicacionBundle\Entity\InstanciaComunicacion'
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'opciones_email';
    }
}