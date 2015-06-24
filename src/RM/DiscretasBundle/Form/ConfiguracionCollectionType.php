<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 22/06/2015
 * Time: 14:36
 */

namespace RM\DiscretasBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfiguracionCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parametros', 'collection', [
                'type'         => new ConfiguracionType(),
                'allow_add'    => false,
                'allow_delete' => false,
            ]);;
    }

    public function getName()
    {
        return 'parametros_configuracion';
    }
} 