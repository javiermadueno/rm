<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 09/07/2015
 * Time: 11:33
 */

namespace RM\DiscretasBundle\Form\Type;

use RM\DiscretasBundle\Form\VariablesBasicas\ModificarCriteriosNyMType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class VidCriteriosGlobalesCollectionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('criterios', 'collection', [
            'allow_add'    => false,
            'allow_delete' => false,
            'type'         => new ModificarCriteriosNyMType(),
        ]);
    }


    public function getName()
    {
        return 'criterios_globales';
    }

} 