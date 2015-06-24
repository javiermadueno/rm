<?php

namespace RM\ComunicacionBundle\Form\Gestion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class faseConfirmacionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
            ->add('fecha', 'date', [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
            ])
            ->add('envio',
                'choice', [
                    'choices'  => [
                        '0' => 'Env�o inmediato (al tramitar a finalizado)',
                        '1' => 'Env�o programado para el d�a: ',
                    ],
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                ]);
    }

    public function getName()
    {
        return '';
    }
}