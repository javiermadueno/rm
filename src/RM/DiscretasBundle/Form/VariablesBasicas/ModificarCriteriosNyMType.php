<?php

namespace RM\DiscretasBundle\Form\VariablesBasicas;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModificarCriteriosNyMType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenciaN', 'integer', [
                'label'    => 'Tiempo Referencia N',
                'required' => true,
            ])
            ->add('mesesN', 'integer', [
                'label'    => 'N',
                'required' => true,
            ])
            ->add('mesesM', 'integer', [
                'label'    => 'M',
                'required' => true,
            ]);
    }

    public function getName()
    {
        return '';
    }
}

