<?php

namespace RM\TransformadasBundle\Form\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NuevaVarTransType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre');
        $builder->add('descripcion');

    }

    public function getName()
    {
        return '';
    }
}

