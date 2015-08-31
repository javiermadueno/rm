<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 13/08/2015
 * Time: 9:36
 */

namespace RM\ProductoBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CsvImagenesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', [
            'required'=> true,
            'label'   => 'formulario_imagenes_csv.file.label'
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\CsvImagenes'
        ]);
    }

    public function getName()
    {
        return 'imagenes_csv';
    }

} 