<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 15/06/2015
 * Time: 13:13
 */

namespace RM\DiscretasBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FranjaHorariaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valor_inicio', 'time', [
                'input' => 'datetime',
                'html5' => true,
                'required' => true,
                'widget' =>'single_text'
            ])
            ->add('valor_fin', 'time', [
                'input' => 'datetime',
                'html5' => true,
                'required' => true,
                'widget' =>'single_text'

            ])
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RM\DiscretasBundle\Entity\FranjaHoraria'
        ));
    }

    public function getName()
    {
        return 'franja_horaria';
    }
} 